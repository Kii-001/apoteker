// Enhanced cart functionality
class ShoppingCart {
    constructor() {
        this.items = this.loadCart();
        this.updateCartUI();
    }

    loadCart() {
        return JSON.parse(localStorage.getItem('apotek_cart')) || [];
    }

    saveCart() {
        localStorage.setItem('apotek_cart', JSON.stringify(this.items));
        this.updateCartUI();
    }

    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                image: product.image
            });
        }
        
        this.saveCart();
        this.showNotification('Produk berhasil ditambahkan ke keranjang');
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveCart();
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
            if (item.quantity <= 0) {
                this.removeItem(productId);
            } else {
                this.saveCart();
            }
        }
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    getItemCount() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    clear() {
        this.items = [];
        this.saveCart();
    }

    updateCartUI() {
        // Update cart count in header
        const cartCounts = document.querySelectorAll('.cart-count');
        const count = this.getItemCount();
        
        cartCounts.forEach(cartCount => {
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.style.display = 'flex';
            } else {
                cartCount.style.display = 'none';
            }
        });

        // Update cart modal if open
        this.updateCartModal();
    }

    updateCartModal() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal && cartModal.classList.contains('show')) {
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            
            if (this.items.length === 0) {
                cartItems.innerHTML = '<div class="empty-cart-message">Keranjang belanja kosong</div>';
                cartTotal.textContent = 'Rp 0';
            } else {
                cartItems.innerHTML = this.items.map(item => `
                    <div class="cart-modal-item">
                        <img src="assets/uploads/${item.image || 'default.jpg'}" alt="${item.name}">
                        <div class="item-details">
                            <h4>${item.name}</h4>
                            <p>${this.formatRupiah(item.price)}</p>
                        </div>
                        <div class="item-controls">
                            <button onclick="cart.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button onclick="cart.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                        <button class="remove-btn" onclick="cart.removeItem(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `).join('');
                
                cartTotal.textContent = this.formatRupiah(this.getTotal());
            }
        }
    }

    showNotification(message) {
        // Remove existing notification
        const existingNotification = document.querySelector('.cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create new notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--success);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            max-width: 300px;
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    formatRupiah(amount) {
        return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}

// Initialize cart
const cart = new ShoppingCart();

// Add to cart event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const product = {
                id: this.dataset.id,
                name: productCard.querySelector('h3').textContent,
                price: parseFloat(productCard.querySelector('.product-price').textContent.replace(/[^\d]/g, '')),
                image: productCard.querySelector('img').src.split('/').pop()
            };
            
            cart.addItem(product);
        });
    });

    // Cart modal functionality
    const cartModal = document.getElementById('cartModal');
    if (cartModal) {
        // Update cart when modal is shown
        cartModal.addEventListener('show.bs.modal', function() {
            cart.updateCartModal();
        });

        // Checkout button
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function() {
                if (cart.items.length > 0) {
                    window.location.href = 'checkout.php';
                }
            });
        }
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .cart-modal-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }
    
    .cart-modal-item img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-details h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }
    
    .item-details p {
        margin: 0;
        color: var(--primary);
        font-weight: bold;
    }
    
    .item-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .item-controls button {
        width: 30px;
        height: 30px;
        border: 1px solid var(--border);
        background: white;
        border-radius: 0.25rem;
        cursor: pointer;
    }
    
    .remove-btn {
        background: var(--danger);
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 0.25rem;
        cursor: pointer;
    }
    
    .empty-cart-message {
        text-align: center;
        padding: 2rem;
        color: var(--secondary);
    }
`;
document.head.appendChild(style);