<?php
session_start();
header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$product = $input['product'] ?? null;
$productId = $input['productId'] ?? null;
$change = $input['change'] ?? 0;
$remove = $input['remove'] ?? false;

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

function getCartCount($cart) {
    $count = 0;
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function findCartItem($cart, $productId) {
    foreach ($cart as $key => $item) {
        if ($item['id'] == $productId) {
            return $key;
        }
    }
    return -1;
}

try {
    if ($action === 'add' && $product) {
        // Add item to cart
        $existingIndex = findCartItem($cart, $product['id']);
        
        if ($existingIndex !== -1) {
            // Update existing item quantity
            $newQuantity = $cart[$existingIndex]['quantity'] + $product['quantity'];
            $cart[$existingIndex]['quantity'] = $newQuantity;
        } else {
            // Add new item
            $cart[] = [
                'id' => $product['id'],
                'nama' => $product['name'],
                'harga' => $product['price'],
                'quantity' => $product['quantity'],
                'image' => $product['image'] ?? 'default.jpg'
            ];
        }
        
        $response = [
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cartCount' => getCartCount($cart),
            'cart' => $cart
        ];
        
    } elseif ($remove && $productId) {
        // Remove item from cart
        $index = findCartItem($cart, $productId);
        if ($index !== -1) {
            array_splice($cart, $index, 1);
        }
        
        $response = [
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'cartCount' => getCartCount($cart),
            'cart' => $cart
        ];
        
    } elseif ($productId && $change !== 0) {
        // Update quantity
        $index = findCartItem($cart, $productId);
        if ($index !== -1) {
            $newQuantity = $cart[$index]['quantity'] + $change;
            
            if ($newQuantity <= 0) {
                array_splice($cart, $index, 1);
            } else {
                $cart[$index]['quantity'] = $newQuantity;
            }
            
            $response = [
                'success' => true,
                'message' => 'Quantity berhasil diupdate',
                'cartCount' => getCartCount($cart),
                'cart' => $cart
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Produk tidak ditemukan di keranjang'
            ];
        }
        
    } elseif ($action === 'clear') {
        // Clear cart
        $_SESSION['cart'] = [];
        $response = [
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'cartCount' => 0,
            'cart' => []
        ];
        
    } else {
        $response = [
            'success' => false,
            'message' => 'Aksi tidak valid'
        ];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ];
}

echo json_encode($response);
?>