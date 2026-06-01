document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#admin-form');
    const table = document.querySelector('.data-table');

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const module = form.dataset.module;
            const data = new FormData(form);
            data.append('action', 'save');
            data.append('module', module);

            fetch('index.php?route=ajax', {
                method: 'POST',
                body: data
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message || 'Збережено');
                        window.location.reload();
                    } else {
                        alert(result.message || 'Помилка збереження');
                    }
                });
        });
    }

    if (table) {
        table.addEventListener('click', function (event) {
            const button = event.target.closest('button');
            if (!button) return;
            const action = button.dataset.action;
            const row = button.closest('tr');
            const module = table.dataset.module;
            if (!row || !module) return;

            const id = row.dataset.id || row.querySelector('td')?.textContent.trim();
            if (!id) return;

            if (action === 'delete') {
                if (!confirm('Ви дійсно бажаєте видалити запис?')) {
                    return;
                }
                const data = new FormData();
                data.append('action', 'delete');
                data.append('module', module);
                data.append('id', id);
                fetch('index.php?route=ajax', {
                    method: 'POST',
                    body: data
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert(result.message || 'Видалено');
                            window.location.reload();
                        } else {
                            alert(result.message || 'Не вдалося видалити');
                        }
                    });
            }

            if (action === 'edit') {
                const itemData = row.dataset.item ? JSON.parse(row.dataset.item) : null;
                if (itemData) {
                    const fields = Array.from(form.elements).filter(el => el.name);
                    fields.forEach(field => {
                        const name = field.name;
                        if (itemData[name] !== undefined) {
                            field.value = itemData[name];
                        }
                    });
                }
                form.querySelector('input[name="id"]').value = id;
                window.scrollTo({ top: form.offsetTop - 24, behavior: 'smooth' });
            }
        });
    }

    document.body.addEventListener('change', function (event) {
        if (event.target.matches('.order-status')) {
            const select = event.target;
            const orderId = select.dataset.id;
            const status = select.value;
            const fd = new FormData();
            fd.append('action','order_status');
            fd.append('id', orderId);
            fd.append('status', status);
            fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
                if (!res.success) {
                    alert(res.message || 'Не вдалося оновити статус');
                }
            });
        }
    });

    const catalogSearchForm = document.getElementById('catalog-search-form');
    if (catalogSearchForm) {
        const searchInput = document.getElementById('catalog-search-name');
        const categorySelect = document.getElementById('catalog-search-category');
        const minPriceInput = document.getElementById('catalog-search-min-price');
        const maxPriceInput = document.getElementById('catalog-search-max-price');
        const searchMessage = document.getElementById('catalog-search-message');
        const productCards = Array.from(document.querySelectorAll('.product-card'));

        const applyCatalogFilters = () => {
            const query = (searchInput.value || '').trim().toLowerCase();
            const category = (categorySelect.value || '').trim();
            const minPrice = parseFloat(minPriceInput.value) || 0;
            const maxPrice = parseFloat(maxPriceInput.value) || 0;
            let visibleCount = 0;

            productCards.forEach(card => {
                const item = JSON.parse(card.dataset.item || '{}');
                const name = (item.name || '').toLowerCase();
                const cat = (item.category || '').trim();
                const price = parseFloat(item.price) || 0;
                const matchesName = query === '' || name.includes(query);
                const matchesCategory = category === '' || cat === category;
                const matchesMinPrice = minPrice <= 0 || price >= minPrice;
                const matchesMaxPrice = maxPrice <= 0 || price <= maxPrice;
                if (matchesName && matchesCategory && matchesMinPrice && matchesMaxPrice) {
                    card.style.display = '';
                    visibleCount += 1;
                } else {
                    card.style.display = 'none';
                }
            });

            if (searchMessage) {
                searchMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        };

        searchInput.addEventListener('input', applyCatalogFilters);
        categorySelect.addEventListener('change', applyCatalogFilters);
        minPriceInput.addEventListener('input', applyCatalogFilters);
        maxPriceInput.addEventListener('input', applyCatalogFilters);
    }

    const userSearchInput = document.getElementById('user-search');
    if (userSearchInput) {
        const usersTable = document.querySelector('.data-table[data-module="users"] tbody');
        userSearchInput.addEventListener('input', function () {
            const query = (this.value || '').trim().toLowerCase();
            if (!usersTable) return;
            Array.from(usersTable.querySelectorAll('tr')).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});

 
document.addEventListener('click', function (e) {
     
    const quick = e.target.closest('.quick-add');
    if (quick) {
        e.preventDefault();
        e.stopPropagation();
        const id = quick.dataset.id || quick.getAttribute('data-id');
        if (!id) return;
        const fd = new FormData(); fd.append('action','cart_add'); fd.append('id', id); fd.append('qty', 1);
        fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
            if (res.success) {
                updateCartCount();
                flashMessage(res.message||'Додано у кошик');
            } else {
                alert(res.message||'Помилка');
            }
        });
        return;
    }

     
    const card = e.target.closest('.product-card');
    if (card && card.dataset && card.dataset.item) {
        const item = JSON.parse(card.dataset.item);
        openProductModal(item);
        return;
    }

    const mediaCard = e.target.closest('.gallery-card, .news-article');
    if (mediaCard && mediaCard.dataset) {
        openMediaModal({
            image: mediaCard.dataset.image || '',
            title: mediaCard.dataset.title || '',
            description: mediaCard.dataset.description || '',
            date: mediaCard.dataset.date || ''
        });
        return;
    }

    const commentDelete = e.target.closest('.comment-delete');
    if (commentDelete) {
        e.preventDefault();
        const commentId = commentDelete.dataset.commentId;
        if (!commentId || !confirm('Видалити цей коментар?')) return;
        const fd = new FormData();
        fd.append('action','comment_delete');
        fd.append('id', commentId);
        fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
            if (res.success) {
                const productId = document.querySelector('.modal-content')?.dataset.productId;
                if (productId) {
                    loadComments(parseInt(productId, 10));
                }
            } else {
                alert(res.message || 'Не вдалося видалити коментар');
            }
        });
    }
});

function openProductModal(item) {
    let modal = document.getElementById('product-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'product-modal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    const currentUser = getUserName();
    const commentFormHtml = currentUser ? `
        <p class="comment-user-info">Коментар буде додано від імені <strong>${escapeHtml(currentUser)}</strong></p>
        <form id="modal-comment-form">
            <label>Оцінка <input type="number" name="rating" value="5" min="1" max="5"></label>
            <textarea name="comment" placeholder="Ваш коментар" required></textarea>
            <button type="submit" class="button">Додати</button>
        </form>
    ` : `
        <div class="comment-login-prompt">
            Будь ласка, <a href="index.php?route=login">увійдіть</a>, щоб залишити коментар.
        </div>
    `;

    modal.innerHTML = `
        <div class="modal-content" data-product-id="${item.id}">
            <button class="modal-close">×</button>
            <div class="modal-grid">
                <div class="modal-image" style="background-image:url('${escapeHtml(item.image || 'https://via.placeholder.com/520x320?text=Фото')}')"></div>
                <div>
                    <h2>${escapeHtml(item.name)}</h2>
                    <p class="modal-category">Категорія: ${escapeHtml(item.category || 'Не вказано')}</p>
                    <p class="modal-description">${escapeHtml(item.description || 'Опис відсутній')}</p>
                    <div class="modal-meta">
                        <span class="modal-price">Ціна: <strong>${Number(item.price).toFixed(2)} ₴</strong> | Рейтинг: <strong id="modal-rating">немає оцінок</strong></span>
                    </div>
                    <div class="modal-stock-rating">
                        <span class="modal-stock-text">Наявність: <strong id="modal-stock">${item.stock}</strong></span>
                    </div>
                    <div class="modal-actions">
                        <label>Кількість <input type="number" id="modal-qty" value="1" min="1" max="${item.stock}"></label>
                        <button id="modal-add" class="button">Додати у кошик</button>
                    </div>
                    <div class="modal-comments-wrapper">
                        <h4>Коментарі</h4>
                        <div id="modal-comments"></div>
                    </div>
                    <div class="modal-comment-form-wrapper">
                        <h5>Додати коментар</h5>
                        ${commentFormHtml}
                    </div>
                </div>
            </div>
        </div>
    `;

    modal.querySelector('.modal-close').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
    modal.querySelector('#modal-add').addEventListener('click', () => {
        const qty = parseInt(document.getElementById('modal-qty').value || '1', 10);
        const data = new FormData();
        data.append('action','cart_add');
        data.append('id', item.id);
        data.append('qty', qty);
        fetch('index.php?route=ajax',{method:'POST',body:data}).then(r=>r.json()).then(res=>{
            if (res.success) {
                alert(res.message || 'Додано');
                updateCartCount();
            } else alert(res.message||'Помилка');
        });
    });

    loadComments(item.id);
    const form = modal.querySelector('#modal-comment-form');
    if (form) {
        form.addEventListener('submit', function (ev) {
            ev.preventDefault();
            const fd = new FormData(form);
            fd.append('action','comments_add');
            fd.append('product_id', item.id);
            fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
                if (res.success) { loadComments(item.id); form.reset(); } else alert(res.message||'Помилка');
            });
        });
    }
}

function renderStarRating(value) {
    const stars = Math.round(value || 0);
    let result = '';
    for (let i = 0; i < 5; i++) {
        result += i < stars ? '★' : '☆';
    }
    return result;
}

function openMediaModal(data) {
    let modal = document.getElementById('media-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'media-modal';
        modal.className = 'modal';
        document.body.appendChild(modal);
    }
    modal.innerHTML = `
        <div class="modal-content media-modal-content">
            <button class="modal-close">×</button>
            ${data.image ? `<div class="modal-image media-modal-image" style="background-image:url('${escapeHtml(data.image)}')"></div>` : ''}
            <div class="media-modal-body">
                <h2>${escapeHtml(data.title)}</h2>
                ${data.date ? `<p class="media-modal-date">${escapeHtml(data.date)}</p>` : ''}
                <p class="modal-description">${escapeHtml(data.description || 'Опис відсутній')}</p>
            </div>
        </div>
    `;
    modal.querySelector('.modal-close').addEventListener('click', () => modal.remove());
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function loadComments(productId) {
    const fd = new FormData(); fd.append('action','comments_get'); fd.append('product_id', productId);
    fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
        const holder = document.getElementById('modal-comments');
        const ratingEl = document.getElementById('modal-rating');
        if (!holder) return;
        holder.innerHTML = '';
        if (res.success && Array.isArray(res.comments) && res.comments.length) {
            const avg = res.comments.reduce((sum, c) => sum + (parseInt(c.rating, 10) || 0), 0) / res.comments.length;
            if (ratingEl) {
                ratingEl.textContent = avg.toFixed(1) + ' / 5';
                ratingEl.insertAdjacentHTML('beforeend', ` <span class="comment-stars">${renderStarRating(avg)}</span>`);
            }
            const currentUser = getUserName();
            res.comments.forEach(c=>{
                const el = document.createElement('div'); el.className='comment';
                let content = `<strong>${escapeHtml(c.author)}</strong> <span class="comment-rating">⭐ ${escapeHtml(c.rating)}</span><div>${escapeHtml(c.comment)}</div>`;
                if (getUserRole() === 'admin' || (currentUser && currentUser === c.author)) {
                    content += `<button class="button button-secondary comment-delete" data-comment-id="${c.id}">Видалити</button>`;
                }
                el.innerHTML = content;
                holder.appendChild(el);
            });
        } else {
            if (ratingEl) ratingEl.textContent = 'немає оцінок';
            holder.innerHTML = '<p>Поки що немає коментарів.</p>';
        }
    });
}

function getUserName() {
    return document.body.dataset.userName || '';
}

function getUserRole() {
    return document.body.dataset.userRole || 'guest';
}

function escapeHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function syncQuickAddButtons(cartItems) {
    const cartIds = Array.isArray(cartItems) ? cartItems.map(item => String(item.id)) : [];
    document.querySelectorAll('.quick-add').forEach(button => {
        const itemId = button.dataset.id || button.getAttribute('data-id');
        if (cartIds.includes(String(itemId))) {
            button.textContent = 'У кошику';
            button.disabled = true;
            button.classList.add('added');
        } else {
            button.textContent = '+';
            button.disabled = false;
            button.classList.remove('added');
        }
    });
}

function updateCartCount(){
    fetch('index.php?route=ajax',{method:'POST',body:(()=>{const d=new FormData();d.append('action','cart_get');return d})()}).then(r=>r.json()).then(res=>{
        const el=document.getElementById('cart-count'); if(el) el.textContent = (res.cart||[]).reduce((s,i)=>s+ (i.qty?parseInt(i.qty,10):1),0);
        if (res.success) {
            syncQuickAddButtons(res.cart);
        }
    });
}

 
document.addEventListener('DOMContentLoaded', function(){
    const toggle = document.getElementById('theme-toggle');
    const cartLink = document.getElementById('cart-link');
    const current = localStorage.getItem('theme') || 'light'; setTheme(current);
    if (toggle) {
        toggle.addEventListener('click', function(e){ e.preventDefault(); setTheme(document.documentElement.dataset.theme==='dark'?'light':'dark'); });
    }
    if (cartLink) {
        cartLink.href = 'javascript:void(0)';
        cartLink.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            openCartModal();
        });
    }
    updateCartCount();
});

function setTheme(name){ document.documentElement.dataset.theme = name; localStorage.setItem('theme',name); }

function flashMessage(text, timeout=2000){
    let el = document.getElementById('flash-message');
    if (!el) { el = document.createElement('div'); el.id='flash-message'; el.style.position='fixed'; el.style.right='16px'; el.style.bottom='16px'; el.style.background='rgba(0,0,0,0.7)'; el.style.color='#fff'; el.style.padding='10px 14px'; el.style.borderRadius='8px'; el.style.zIndex=99999; el.style.transition='opacity 0.3s ease'; document.body.appendChild(el); }
    el.textContent = text; el.style.opacity = '1';
    setTimeout(()=>{ if (el) el.style.opacity='0'; }, timeout);
}

function openCartModal() {
    let modal = document.getElementById('cart-modal');
    if (modal) {
        modal.remove();
    }
    modal = document.createElement('div');
    modal.id = 'cart-modal';
    modal.className = 'modal';
    document.body.appendChild(modal);
    const fd = new FormData(); fd.append('action','cart_get');
    fetch('index.php?route=ajax',{method:'POST',body:fd}).then(r=>r.json()).then(res=>{
        if (!res.success) {
            alert(res.message || 'Не вдалося отримати кошик');
            return;
        }
        const items = Array.isArray(res.cart) ? res.cart : [];
        const total = items.reduce((sum,item) => sum + (parseFloat(item.price) || 0) * (parseInt(item.qty,10) || 1), 0);
        const isGuest = getUserRole() === 'guest';
        modal.innerHTML = `
            <div class="modal-content cart-modal-content">
                <button class="modal-close">×</button>
                <h2>Кошик</h2>
                <div class="cart-items">
                    ${items.length ? items.map(item => `
                        <div class="cart-row" data-id="${item.id}">
                            <div>
                                <div class="cart-name">${escapeHtml(item.name)}</div>
                                <div class="cart-details">${escapeHtml(item.qty + ' x ' + Number(item.price).toFixed(2))} ₴</div>
                            </div>
                            <button class="button button-secondary cart-remove" data-id="${item.id}">Видалити</button>
                        </div>
                    `).join('') : '<div class="cart-empty"><p>Кошик порожній.</p></div>'}
                </div>
                <div class="cart-footer">
                    <strong>Підсумок: ${total.toFixed(2)} ₴</strong>
                    <button id="cart-checkout" class="button" ${items.length && !isGuest ? '' : 'disabled'}>${isGuest ? 'Увійдіть для оформлення' : 'Оформити замовлення'}</button>
                </div>
                ${isGuest ? '<p class="cart-note">Тільки авторизовані користувачі можуть оформити замовлення.</p>' : ''}
            </div>
        `;
        const closeButton = modal.querySelector('.modal-close');
        if (closeButton) closeButton.addEventListener('click', () => modal.remove());
        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.remove();
        });
        modal.querySelectorAll('.cart-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const fd2 = new FormData(); fd2.append('action','cart_remove'); fd2.append('id', id);
                fetch('index.php?route=ajax',{method:'POST',body:fd2}).then(r=>r.json()).then(rem=>{
                    if (rem.success) {
                        openCartModal();
                        updateCartCount();
                    } else {
                        alert(rem.message||'Не вдалося видалити');
                    }
                });
            });
        });
        const checkoutBtn = modal.querySelector('#cart-checkout');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function () {
                const fd3 = new FormData(); fd3.append('action','checkout');
                fetch('index.php?route=ajax',{method:'POST',body:fd3}).then(r=>r.json()).then(ch=>{
                    if (ch.success) {
                        alert(ch.message || 'Замовлення оформлено');
                        modal.remove();
                        updateCartCount();
                    } else {
                        alert(ch.message || 'Не вдалося оформити замовлення');
                    }
                });
            });
        }
    });
}
