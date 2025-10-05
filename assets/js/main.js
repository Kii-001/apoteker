// Cart functionality
class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
        this.updateCartCount();
    }

    addItem(productId, productName, price, quantity = 1) {
        const existingItem = this.items.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                id: productId,
                name: productName,
                price: price,
                quantity: quantity
            });
        }
        
        this.save();
        this.updateCartCount();
        this.showNotification('Produk berhasil ditambahkan ke keranjang');
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.save();
        this.updateCartCount();
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = quantity;
            if (item.quantity <= 0) {
                this.removeItem(productId);
            } else {
                this.save();
            }
        }
    }

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    clear() {
        this.items = [];
        this.save();
        this.updateCartCount();
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    }

    updateCartCount() {
        const count = this.items.reduce((total, item) => total + item.quantity, 0);
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = count;
        }
    }

    showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'notification';
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
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize cart
const cart = new Cart();

// Add to cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.closest('.product-info').querySelector('h3').textContent;
            const productPrice = parseFloat(this.closest('.product-info').querySelector('.product-price').textContent.replace('Rp ', '').replace('.', ''));
            
            cart.addItem(productId, productName, productPrice);
        });
    });

    // Cart modal functionality
    const cartModal = document.getElementById('cartModal');
    if (cartModal) {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const checkoutBtn = document.getElementById('checkoutBtn');

        function updateCartModal() {
            if (cart.items.length === 0) {
                cartItems.innerHTML = '<p class="empty-cart">Keranjang belanja kosong</p>';
                checkoutBtn.disabled = true;
            } else {
                cartItems.innerHTML = cart.items.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.name}</h4>
                            <p>${formatRupiah(item.price)} x ${item.quantity}</p>
                        </div>
                        <div class="cart-item-actions">
                            <button class="btn-quantity" onclick="cart.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button class="btn-quantity" onclick="cart.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                            <button class="btn-remove" onclick="cart.removeItem(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                checkoutBtn.disabled = false;
            }
            cartTotal.textContent = formatRupiah(cart.getTotal());
        }

        // Format Rupiah helper
        window.formatRupiah = function(amount) {
            return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        // Expose cart to global scope for button clicks
        window.cart = cart;
        window.updateCartModal = updateCartModal;

        // Update modal when opened
        cartModal.addEventListener('show.bs.modal', updateCartModal);
    }
});