import requests
from bs4 import BeautifulSoup
import os
import re

# Folders
IMAGE_FOLDER = "assets/uploads"
os.makedirs(IMAGE_FOLDER, exist_ok=True)

SQL_FILE = "products.sql"

BASE_URL = "https://itechstore.com.np/products/page/"

# Helper function to sanitize SQL strings
def sql_escape(s):
    return s.replace("'", "''")

# Get all product URLs
def get_product_urls(max_pages=50):
    product_urls = []
    for page in range(1, max_pages+1):
        url = f"{BASE_URL}{page}"
        r = requests.get(url)
        if r.status_code != 200:
            break
        soup = BeautifulSoup(r.text, "html.parser")
        links = soup.select("a.product-title")
        if not links:
            break
        for link in links:
            href = link.get("href")
            if href:
                product_urls.append(href)
        print(f"Page {page}: found {len(links)} products")
    return product_urls

# Download image locally
def download_image(img_url, product_name):
    if not img_url:
        return ""
    ext = os.path.splitext(img_url)[1].split("?")[0]
    safe_name = re.sub(r'[^a-zA-Z0-9_-]', '_', product_name)[:50]
    local_path = os.path.join(IMAGE_FOLDER, f"{safe_name}{ext}")
    try:
        r = requests.get(img_url)
        if r.status_code == 200:
            with open(local_path, "wb") as f:
                f.write(r.content)
            return local_path.replace("\\", "/")
    except:
        pass
    return ""

# Scrape product details
def scrape_product(url):
    r = requests.get(url)
    if r.status_code != 200:
        return None

    soup = BeautifulSoup(r.text, "html.parser")

    # Name
    name_tag = soup.select_one("h1.product-title")
    name = name_tag.text.strip() if name_tag else None

    # Price
    price_tag = soup.select_one(".woocommerce-Price-amount")
    price = None
    if price_tag:
        price_text = price_tag.text.strip()
        price = re.sub(r'[^\d.]', '', price_text)
        price = float(price) if price else None

    # Description
    desc_tag = soup.select_one("div.woocommerce-product-details__short-description")
    description = desc_tag.text.strip() if desc_tag else ""

    # Image
    img_tag = soup.select_one(".woocommerce-product-gallery__image img")
    img_url = img_tag.get("src") if img_tag else ""
    image_path = download_image(img_url, name) if img_url else ""

    # Category
    cat_tag = soup.select_one("span.posted_in a")
    category = cat_tag.text.strip() if cat_tag else "Uncategorized"

    if not name or not price or not description:
        return None  # skip products with missing info

    return {
        "name": name,
        "price": price,
        "description": description,
        "image": image_path,
        "category": category
    }

# Main
def main():
    product_urls = get_product_urls()
    print(f"Total products found: {len(product_urls)}")

    categories = {}
    sql_lines = ["-- Sample products\n"]

    product_id = 1
    category_id_counter = 1

    for idx, url in enumerate(product_urls, 1):
        print(f"[{idx}] Scraping: {url}")
        data = scrape_product(url)
        if not data:
            continue

        # Category mapping
        cat_name = data["category"]
        if cat_name not in categories:
            categories[cat_name] = category_id_counter
            sql_lines.append(f"INSERT INTO `category` (`category_id`, `category_name`) VALUES ({category_id_counter}, '{sql_escape(cat_name)}');")
            category_id_counter += 1

        cat_id = categories[cat_name]

        # Add product
        sql_lines.append(
            f"INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image_path`, `product_stock`, `category_id`) VALUES "
            f"({product_id}, '{sql_escape(data['name'])}', '{sql_escape(data['description'])}', {data['price']}, '{data['image']}', 10, {cat_id});"
        )
        product_id += 1

    # Save SQL file
    with open(SQL_FILE, "w", encoding="utf-8") as f:
        f.write("\n".join(sql_lines))

    print(f"SQL file saved to {SQL_FILE}")
    print(f"Images saved to {IMAGE_FOLDER}")

if __name__ == "__main__":
    main()
