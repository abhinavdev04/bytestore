import os
import re
import time
import requests
import mysql.connector
from bs4 import BeautifulSoup
from urllib.parse import urlparse

# -----------------------------
# CONFIGURATION
# -----------------------------
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",          # change if needed
    "database": "bytestore",
    "charset": "utf8mb4"
}

IMAGE_DIR = "assets/uploads/products"
os.makedirs(IMAGE_DIR, exist_ok=True)

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Educational Project)"
}

# -----------------------------
# DATABASE CONNECTION
# -----------------------------
db = mysql.connector.connect(**DB_CONFIG)
cursor = db.cursor()

# -----------------------------
# HELPERS
# -----------------------------
def slugify(text):
    text = text.lower()
    text = re.sub(r"[^a-z0-9]+", "-", text)
    return text.strip("-")

def clean_price(text):
    price = re.sub(r"[^\d]", "", text)
    return float(price) if price else 0.0

# -----------------------------
# GET OR CREATE CATEGORY
# -----------------------------
def get_or_create_category(category_name):
    cursor.execute(
        "SELECT category_id FROM category WHERE category_name = %s",
        (category_name,)
    )
    row = cursor.fetchone()

    if row:
        return row[0]

    cursor.execute(
        "INSERT INTO category (category_name) VALUES (%s)",
        (category_name,)
    )
    db.commit()
    return cursor.lastrowid

# -----------------------------
# DOWNLOAD IMAGE LOCALLY
# -----------------------------
def download_image(img_url, product_name):
    try:
        parsed = urlparse(img_url)
        ext = os.path.splitext(parsed.path)[1] or ".jpg"
        filename = slugify(product_name) + ext
        local_path = os.path.join(IMAGE_DIR, filename)

        if os.path.exists(local_path):
            return local_path

        r = requests.get(img_url, headers=HEADERS, timeout=20)
        with open(local_path, "wb") as f:
            f.write(r.content)

        return local_path
    except:
        return "assets/uploads/default.jpg"

# -----------------------------
# GET PRODUCT URLS FROM SITEMAP
# -----------------------------
def get_product_urls():
    r = requests.get(
        "https://itechstore.com.np/sitemap.xml",
        headers=HEADERS,
        timeout=20
    )
    soup = BeautifulSoup(r.text, "xml")
    return [loc.text for loc in soup.find_all("loc") if "/product/" in loc.text]

# -----------------------------
# SCRAPE SINGLE PRODUCT
# -----------------------------
def scrape_product(url):
    r = requests.get(url, headers=HEADERS, timeout=20)
    soup = BeautifulSoup(r.text, "html.parser")

    # Product Name
    h1 = soup.find("h1")
    name = h1.get_text(strip=True) if h1 else "Unknown Product"

    # Category (breadcrumb)
    breadcrumb = soup.select("nav a")
    categories = [
        a.get_text(strip=True)
        for a in breadcrumb
        if a.get_text(strip=True).lower() != "home"
    ]
    category_name = categories[0] if categories else "Uncategorized"
    category_id = get_or_create_category(category_name)

    # Price
    price = 0.0
    for span in soup.find_all("span"):
        if "Rs." in span.get_text():
            price = clean_price(span.get_text())
            break

    # Description
    desc_div = soup.find("div", class_="product-description")
    description = desc_div.get_text("\n", strip=True) if desc_div else ""

    # Image
    img = soup.find("img")
    img_url = img["src"] if img and img.get("src") else ""
    image_path = download_image(img_url, name)

    return name, category_id, description, price, image_path

# -----------------------------
# INSERT / UPDATE PRODUCT
# -----------------------------
def save_product(name, category_id, desc, price, image):
    sql = """
    INSERT INTO product
    (product_name, category_id, product_description, product_price, product_image_path, product_stock)
    VALUES (%s, %s, %s, %s, %s, 10)
    ON DUPLICATE KEY UPDATE
        product_price = VALUES(product_price),
        product_image_path = VALUES(product_image_path),
        category_id = VALUES(category_id),
        updated_at = CURRENT_TIMESTAMP
    """
    cursor.execute(sql, (name, category_id, desc, price, image))
    db.commit()

# -----------------------------
# MAIN EXECUTION
# -----------------------------
urls = get_product_urls()
print("Total products found:", len(urls))

for i, url in enumerate(urls, start=1):
    try:
        print(f"[{i}] Scraping:", url)
        product_data = scrape_product(url)
        save_product(*product_data)
        time.sleep(1.5)  # rate limit
    except Exception as e:
        print("Error:", e)

cursor.close()
db.close()

print("Scraping completed successfully.")
