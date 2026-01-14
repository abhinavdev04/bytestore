import requests
from bs4 import BeautifulSoup
import os
import time
import random
from urllib.parse import urljoin
import re

class iTechStoreScraper:
    def __init__(self):
        self.base_url = "https://itechstore.com.np"
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }
        self.session = requests.Session()
        self.session.headers.update(self.headers)
        self.categories = {}  # {category_name: category_id}
        self.category_counter = 1
        self.products = []
        self.image_folder = "assets/uploads/products"
        self.category_map = {}  # Maps product URLs to categories
        
        # Create image folder if doesn't exist
        if not os.path.exists(self.image_folder):
            os.makedirs(self.image_folder)
    
    def fetch_category_products(self):
        """Fetch all categories and their products from sitemap"""
        print("Building category map from sitemap...")
        
        categories = {
            'laptops': 'Laptops',
            'desktops': 'Desktops',
            'gaming': 'Gaming',
            'wearables': 'Wearables',
            'phones-tablets-e-reader': 'Phones & Tablets',
            'parts-and-accessories': 'Parts & Accessories',
            'audio-and-video': 'Audio & Video',
            'networking': 'Networking',
            'printers-and-scanners': 'Printers & Scanners',
            'servers': 'Servers',
            'softwares-and-services': 'Software & Services'
        }
        
        for category_slug, category_name in categories.items():
            try:
                url = f"{self.base_url}/category/{category_slug}"
                print(f"  Fetching: {category_name}...")
                response = self.session.get(url, timeout=10)
                soup = BeautifulSoup(response.content, 'html.parser')
                
                # Find all product links
                product_links = soup.find_all('a', href=re.compile(r'/product/'))
                for link in product_links:
                    product_url = link.get('href')
                    if product_url and '/product/' in product_url:
                        full_url = urljoin(self.base_url, product_url)
                        # Map the base product URL (without variants)
                        base_url = full_url.split('?')[0]
                        self.category_map[base_url] = category_name
                
                time.sleep(0.3)  # Be respectful
            except Exception as e:
                print(f"  Error fetching {category_name}: {e}")
        
        print(f"  Mapped {len(self.category_map)} products to categories")
    
    def get_sitemap_urls(self):
        """Extract all product URLs from sitemap, excluding variants"""
        print("Fetching sitemap...")
        sitemap_url = f"{self.base_url}/sitemap.xml"
        
        try:
            response = self.session.get(sitemap_url, timeout=10)
            soup = BeautifulSoup(response.content, 'xml')
            all_urls = [loc.text for loc in soup.find_all('loc') if '/product/' in loc.text]
            
            # Filter out variants and duplicates
            filtered_urls = []
            seen_base_urls = set()
            
            for url in all_urls:
                # Skip URLs with /default or other variant indicators
                if '/default' in url:
                    continue
                
                # Get base product URL (before any variant params)
                base_url = url.split('?')[0]
                
                # Skip if we've seen this base product
                # Check if this is a variant URL (has path after product name)
                parts = base_url.split('/product/')
                if len(parts) > 1:
                    product_parts = parts[1].split('/')
                    # If there are multiple path segments, it's likely a variant
                    if len(product_parts) > 1 and product_parts[1]:
                        continue
                
                if base_url not in seen_base_urls:
                    seen_base_urls.add(base_url)
                    filtered_urls.append(url)
            
            print(f"Found {len(filtered_urls)} unique products (filtered from {len(all_urls)} total URLs)")
            return filtered_urls
        except Exception as e:
            print(f"Error fetching sitemap: {e}")
            return []
    
    def download_image(self, image_url, product_name):
        """Download image and return local path with forward slashes"""
        if not image_url:
            return None
        
        try:
            # Create safe filename from product name
            safe_name = re.sub(r'[^\w\s-]', '', product_name.lower())
            safe_name = re.sub(r'[-\s]+', '-', safe_name)[:50]
            
            # Get image extension
            ext = '.jpg'
            if '.png' in image_url.lower():
                ext = '.png'
            elif '.webp' in image_url.lower():
                ext = '.webp'
            
            filename = f"{safe_name}{ext}"
            # Use os.path.join but convert to forward slashes for SQL
            filepath = os.path.join(self.image_folder, filename)
            
            # Download image
            img_response = self.session.get(image_url, timeout=10)
            if img_response.status_code == 200:
                with open(filepath, 'wb') as f:
                    f.write(img_response.content)
                # Return path with forward slashes for SQL
                return filepath.replace('\\', '/')
        except Exception as e:
            print(f"  Error downloading image: {e}")
        
        return None
    
    def get_or_create_category(self, category_name):
        """Get category ID or create new one"""
        if category_name not in self.categories:
            self.categories[category_name] = self.category_counter
            self.category_counter += 1
        return self.categories[category_name]
    
    def get_category_from_map(self, url):
        """Get category from the pre-built map"""
        # Try exact match first
        if url in self.category_map:
            return self.category_map[url]
        
        # Try base URL without query params
        base_url = url.split('?')[0]
        if base_url in self.category_map:
            return self.category_map[base_url]
        
        # Try matching product slug
        for mapped_url, category in self.category_map.items():
            if '/product/' in url and '/product/' in mapped_url:
                url_slug = url.split('/product/')[-1].split('/')[0].split('?')[0]
                mapped_slug = mapped_url.split('/product/')[-1].split('/')[0].split('?')[0]
                if url_slug == mapped_slug:
                    return category
        
        return "Uncategorized"
    
    def scrape_product(self, url):
        """Scrape individual product page"""
        try:
            print(f"Scraping: {url}")
            response = self.session.get(url, timeout=15)
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Extract product name
            name_tag = soup.find('h1')
            if not name_tag:
                print("  Skipping - No product name found")
                return None
            product_name = name_tag.text.strip()
            
            # Extract price
            price_text = None
            # Look for price in the page
            price_match = re.search(r'₨\.\s*([\d,]+)', response.text)
            if price_match:
                price_text = price_match.group(1).replace(',', '')
            
            if not price_text:
                print(f"  Skipping - No price found")
                return None
            
            # Extract image
            image_url = None
            img_tag = soup.find('img', src=re.compile(r'media\.itechstore\.com\.np'))
            if img_tag:
                image_url = img_tag.get('src')
                if image_url and not image_url.startswith('http'):
                    image_url = urljoin(self.base_url, image_url)
            
            if not image_url:
                print(f"  Skipping - No image found")
                return None
            
            # Extract description - look for the paragraph after h1
            description = None
            # Find the main description paragraph
            h1_tag = soup.find('h1')
            if h1_tag:
                next_p = h1_tag.find_next('p')
                if next_p:
                    desc_text = next_p.text.strip()
                    # Make sure it's not a price or short text
                    if len(desc_text) > 30 and '₨' not in desc_text:
                        description = desc_text
            
            # If no description found, try to build from specs
            if not description:
                specs_table = soup.find('table')
                if specs_table:
                    specs = []
                    rows = specs_table.find_all('tr')[:5]  # First 5 specs
                    for row in rows:
                        cols = row.find_all('td')
                        if len(cols) >= 2:
                            key = cols[0].text.strip()
                            val = cols[1].text.strip()
                            if key and val and len(key) < 30 and len(val) < 100:
                                specs.append(f"{key}: {val}")
                    if specs:
                        description = ", ".join(specs)
            
            # If still no description, skip this product
            if not description or len(description) < 30:
                print(f"  Skipping - No valid description found (len: {len(description) if description else 0})")
                return None
            
            # Get category from pre-built map
            category = self.get_category_from_map(url)
            category_id = self.get_or_create_category(category)
            
            # Download image
            local_image_path = self.download_image(image_url, product_name)
            if not local_image_path:
                print(f"  Skipping - Image download failed")
                return None
            
            # Random stock if not available
            stock = random.randint(5, 15)
            
            product = {
                'name': product_name,
                'description': description[:500],  # Limit to 500 chars
                'price': price_text,
                'image_path': local_image_path,
                'stock': stock,
                'category': category,
                'category_id': category_id
            }
            
            print(f"  ✓ {product_name} -> {category}")
            return product
            
        except Exception as e:
            print(f"  Error scraping {url}: {e}")
            return None
    
    def scrape_all(self, limit=None):
        """Scrape all products from sitemap"""
        # First, build category map
        self.fetch_category_products()
        
        # Then get product URLs
        urls = self.get_sitemap_urls()
        
        if limit:
            urls = urls[:limit]
        
        total = len(urls)
        for idx, url in enumerate(urls, 1):
            print(f"\n[{idx}/{total}] ", end="")
            product = self.scrape_product(url)
            
            if product:
                self.products.append(product)
            
            # Be respectful - add delay
            time.sleep(0.5)
        
        print(f"\n\nScraping complete!")
        print(f"Total products scraped: {len(self.products)}")
        print(f"Total categories: {len(self.categories)}")
    
    def generate_sql_files(self):
        """Generate SQL INSERT statements matching your database schema"""
        
        # Generate categories SQL
        categories_sql = "-- Categories INSERT statements\n"
        categories_sql += "-- Run this FIRST to create all categories\n\n"
        
        for category_name, category_id in sorted(self.categories.items(), key=lambda x: x[1]):
            safe_category = category_name.replace("'", "''")
            categories_sql += f"INSERT INTO category (category_name) VALUES ('{safe_category}');\n"
        
        # Write categories file
        with open('categories_insert.sql', 'w', encoding='utf-8') as f:
            f.write(categories_sql)
        
        print("\n✓ Created: categories_insert.sql")
        
        # Generate products SQL - EXACTLY matching your schema
        products_sql = "-- Products INSERT statements\n"
        products_sql += "-- Run this AFTER categories are inserted\n"
        products_sql += "-- Matches your existing product table structure:\n"
        products_sql += "-- (product_name, product_description, product_price, product_image_path, product_stock)\n\n"
        
        for product in self.products:
            name = product['name'].replace("'", "''")
            desc = product['description'].replace("'", "''")
            price = product['price']
            image = product['image_path'].replace("'", "''")
            stock = product['stock']
            
            products_sql += f"INSERT INTO product (product_name, product_description, product_price, product_image_path, product_stock) VALUES\n"
            products_sql += f"('{name}', '{desc}', {price}, '{image}', {stock});\n"
        
        # Write products file
        with open('products_insert.sql', 'w', encoding='utf-8') as f:
            f.write(products_sql)
        
        print("✓ Created: products_insert.sql")
        
        # Also create a mapping file showing which products belong to which category
        mapping_sql = "-- Category to Product Mapping (for reference)\n"
        mapping_sql += "-- This shows which products belong to which category\n\n"
        
        for category_name, category_id in sorted(self.categories.items(), key=lambda x: x[1]):
            mapping_sql += f"-- Category: {category_name}\n"
            category_products = [p for p in self.products if p['category_id'] == category_id]
            for product in category_products:
                mapping_sql += f"--   - {product['name']}\n"
            mapping_sql += "\n"
        
        with open('category_product_mapping.txt', 'w', encoding='utf-8') as f:
            f.write(mapping_sql)
        
        print("✓ Created: category_product_mapping.txt (reference)")
        print(f"\nTotal SQL statements generated: {len(self.products)} products, {len(self.categories)} categories")

# Main execution
if __name__ == "__main__":
    scraper = iTechStoreScraper()
    
    print("=" * 60)
    print("iTechStore.com.np Web Scraper")
    print("=" * 60)
    
    # You can limit the number for testing: scraper.scrape_all(limit=10)
    # For all products, use: scraper.scrape_all()
    
    # Start with 10 products for testing
    print("\nStarting scrape (testing with 10 products)...")
    print("Change limit=10 to scraper.scrape_all() for all products\n")
    
    scraper.scrape_all(limit=2000)
    
    # Generate SQL files
    if scraper.products:
        scraper.generate_sql_files()
        print("\n" + "=" * 60)
        print("Scraping completed successfully!")
        print(f"Files created:")
        print(f"  1. categories_insert.sql (run FIRST)")
        print(f"  2. products_insert.sql (run SECOND)")
        print(f"  3. category_product_mapping.txt (reference only)")
        print(f"  4. Images in '{scraper.image_folder}/' folder")
        print("=" * 60)
    else:
        print("\nNo products were scraped. Check the errors above.")