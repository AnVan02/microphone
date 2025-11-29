from flask import Flask, request, jsonify
import mysql.connector
from mysql.connector import Error
import os

app = Flask(__name__)

# ============================================
# CẤU HÌNH DATABASE - SỬA PHẦN NÀY
# ============================================
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': 'your_password',  # Đổi password
    'database': 'your_database',  # Đổi tên database
    'charset': 'utf8mb4'
}

# ============================================
# HÀM KẾT NỐI DATABASE
# ============================================
def get_db_connection():
    """Tạo kết nối đến MySQL database"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        return connection
    except Error as e:
        print(f"Lỗi kết nối database: {e}")
        return None

# ============================================
# API ENDPOINT: KIỂM TRA SẢN PHẨM
# ============================================
@app.route('/api/check-product', methods=['GET', 'POST'])
def check_product():
    """
    API kiểm tra sản phẩm có tồn tại trong database hay không
    
    GET:  /api/check-product?product_id=123
    POST: /api/check-product với JSON body: {"product_id": 123}
    """
    
    # Lấy product_id từ request
    product_id = None
    
    if request.method == 'GET':
        # Lấy từ query parameter
        product_id = request.args.get('product_id')
    
    elif request.method == 'POST':
        # Lấy từ JSON body hoặc form data
        if request.is_json:
            data = request.get_json()
            product_id = data.get('product_id')
        else:
            product_id = request.form.get('product_id')
    
    # Kiểm tra product_id có được gửi lên không
    if not product_id:
        return jsonify({
            'success': False,
            'error': 'Thiếu tham số product_id',
            'usage': {
                'GET': '/api/check-product?product_id=123',
                'POST': 'Gửi JSON: {"product_id": 123} hoặc form-data: product_id=123'
            }
        }), 400
    
    # Validate product_id phải là số
    try:
        product_id = int(product_id)
    except (ValueError, TypeError):
        return jsonify({
            'success': False,
            'error': 'product_id phải là số nguyên',
            'received': str(product_id)
        }), 400
    
    # Kết nối database
    connection = get_db_connection()
    if not connection:
        return jsonify({
            'success': False,
            'error': 'Không thể kết nối database'
        }), 500
    
    try:
        cursor = connection.cursor(dictionary=True)
        
        # Truy vấn sản phẩm
        query = """
            SELECT 
                product_id,
                product_name,
                product_category,
                product_brand,
                capacity_id,
                product_quantity,
                quantity_sales,
                product_price_import,
                product_price,
                product_sale,
                product_description,
                product_image,
                product_status
            FROM product 
            WHERE product_id = %s
        """
        
        cursor.execute(query, (product_id,))
        product = cursor.fetchone()
        
        cursor.close()
        connection.close()
        
        # ============================================
        # TRẢ VỀ KẾT QUẢ
        # ============================================
        
        if product:
            # ✅ TÌM THẤY SẢN PHẨM
            return jsonify({
                'success': True,
                'message': 'Tìm thấy sản phẩm',
                'exists': True,
                'data': {
                    'product_id': product['product_id'],
                    'product_name': product['product_name'],
                    'product_category': product['product_category'],
                    'product_brand': product['product_brand'],
                    'capacity_id': product['capacity_id'],
                    'product_quantity': product['product_quantity'],
                    'quantity_sales': product['quantity_sales'],
                    'product_price_import': float(product['product_price_import']),
                    'product_price': float(product['product_price']),
                    'product_sale': product['product_sale'],
                    'product_description': product['product_description'],
                    'product_image': product['product_image'],
                    'product_status': product['product_status'],
                    'is_available': product['product_status'] == 1 and product['product_quantity'] > 0
                }
            }), 200
        else:
            # ❌ KHÔNG TÌM THẤY SẢN PHẨM
            return jsonify({
                'success': False,
                'message': 'Không tìm thấy sản phẩm',
                'exists': False,
                'product_id': product_id,
                'data': None
            }), 404
    
    except Error as e:
        return jsonify({
            'success': False,
            'error': 'Lỗi truy vấn database',
            'message': str(e)
        }), 500
    finally:
        if connection and connection.is_connected():
            connection.close()

# ============================================
# API ENDPOINT: TRANG CHỦ (Hướng dẫn)
# ============================================
@app.route('/')
def home():
    """Trang chủ hiển thị hướng dẫn sử dụng API"""
    return jsonify({
        'message': 'API Kiểm tra Sản phẩm',
        'version': '1.0',
        'endpoints': {
            'check_product': {
                'url': '/api.php',
                'methods': ['GET', 'POST'],
                'description': 'Kiểm tra sản phẩm có tồn tại trong database',
                'examples': {
                    'GET': 'http://localhost/api.php?product_id=1',
                    'POST': {
                        'url': 'http://localhost/api.php',
                        'body': {'product_id': 1}
                    }
                }
            }
        }
    })

# ============================================
# CHẠY SERVER
# ============================================
if __name__ == '__main__':
    print("=" * 50)
    print("🚀 API Server đang chạy...")
    print("=" * 50)
    print(f"📍 URL: http://localhost:5000")
    print(f"📋 Hướng dẫn: http://localhost:5000")
    print(f"🔍 Kiểm tra sản phẩm: http://localhost/api/check-product?product_id=1")
    print("=" * 50)
    
    # Chạy server
    app.run(
        host='0.0.0.0',  # Cho phép truy cập từ bên ngoài
        port=5000,
        debug=True  # Bật chế độ debug (tắt khi deploy production)
    )