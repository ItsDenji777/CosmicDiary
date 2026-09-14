document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('main').classList.add('fade-ready');
});

const canvas = document.getElementById('starfield');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let stars = [];

// Creates one randomized star for the animated starfield background.
function createStar() {
    return {
        x: (Math.random() - 0.5) * canvas.width * 2,
        y: (Math.random() - 0.5) * canvas.height * 2,
        z: Math.random() * canvas.width
    };
}

for (let i = 0; i < 800; i++) {
    stars.push(createStar());
}

// Renders the moving starfield animation frame by frame.
function animateStars() {
    ctx.fillStyle = 'rgba(10, 10, 26, 0.65)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;

    stars.forEach(star => {
        const prevZ = star.z;
        star.z -= 3;

        if (star.z <= 0) {
            star.x = (Math.random() - 0.5) * canvas.width * 2;
            star.y = (Math.random() - 0.5) * canvas.height * 2;
            star.z = canvas.width;
            return;
        }

        const sx = (star.x / star.z) * canvas.width + centerX;
        const sy = (star.y / star.z) * canvas.height + centerY;
        const prevSx = (star.x / prevZ) * canvas.width + centerX;
        const prevSy = (star.y / prevZ) * canvas.height + centerY;

        const brightness = 1 - star.z / canvas.width;
        const size = brightness * 3;

        if (star.z < canvas.width - 5) {
            ctx.strokeStyle = `rgba(180, 210, 255, ${brightness * 0.7})`;
            ctx.lineWidth = size * 0.6;
            ctx.lineCap = 'round';
            ctx.beginPath();
            ctx.moveTo(prevSx, prevSy);
            ctx.lineTo(sx, sy);
            ctx.stroke();
        }

        ctx.fillStyle = `rgba(255, 255, 255, ${brightness})`;
        ctx.beginPath();
        ctx.arc(sx, sy, size, 0, Math.PI * 2);
        ctx.fill();
    });

    requestAnimationFrame(animateStars);
}
animateStars();

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    stars = [];
    for (let i = 0; i < 800; i++) {
        stars.push(createStar());
    }
});

// Shows a temporary success or error toast message.
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Opens the custom confirmation modal and wires its callbacks.
function showConfirm(title, message, icon, onConfirm) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmIcon').className = 'fas ' + icon;
    document.getElementById('confirmModal').style.display = 'flex';

    const okBtn = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');

    // Closes the confirmation modal and removes its listeners.
    const close = () => {
        document.getElementById('confirmModal').style.display = 'none';
        okBtn.removeEventListener('click', handleOk);
        cancelBtn.removeEventListener('click', close);
    };

    // Confirms the dialog action and runs the provided callback.
    const handleOk = () => {
        close();
        if (onConfirm) onConfirm();
    };

    okBtn.addEventListener('click', handleOk);
    cancelBtn.addEventListener('click', close);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('main').classList.add('fade-ready');

    const overlay = document.getElementById('warpOverlay');

    document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript"]):not([onclick]):not([download])').forEach(link => {
        const href = link.getAttribute('href');
        if (href && !href.startsWith('http') && !href.startsWith('//') && !href.startsWith('#')) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                overlay.classList.add('active');
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            });
        }
    });

    if (overlay && overlay.classList.contains('active')) {
        overlay.classList.remove('active');
    }

    const celebration = sessionStorage.getItem('celebrate');
    if (celebration) {
        sessionStorage.removeItem('celebrate');
        setTimeout(celebrate, 300);
    }

    const rocketBtn = document.getElementById('rocketTop');
    if (rocketBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                rocketBtn.classList.add('visible');
            } else {
                rocketBtn.classList.remove('visible');
            }
        });
        rocketBtn.addEventListener('click', () => {
            const rect = rocketBtn.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;

            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.className = 'rocket-particle';
                const angle = (Math.random() * Math.PI * 2);
                const distance = 20 + Math.random() * 40;
                particle.style.setProperty('--dx', Math.cos(angle) * distance + 'px');
                particle.style.setProperty('--dy', Math.sin(angle) * distance + 'px');
                particle.style.left = cx + 'px';
                particle.style.top = cy + 'px';
                document.body.appendChild(particle);
                setTimeout(() => particle.remove(), 600);
            }

            rocketBtn.classList.add('launching');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            setTimeout(() => {
                rocketBtn.classList.remove('launching');
            }, 800);
        });
    }

    const contextMenu = document.createElement('div');
    contextMenu.className = 'cosmic-context-menu';
    contextMenu.id = 'cosmicContextMenu';
    contextMenu.innerHTML = `
        <button class="cosmic-context-menu-item" data-action="home"><i class="fas fa-home"></i> Home</button>
        <button class="cosmic-context-menu-item" data-action="gallery"><i class="fas fa-globe"></i> Galaxy Gallery</button>
        <button class="cosmic-context-menu-item" data-action="logs"><i class="fas fa-book"></i> Captain's Log</button>
        <div class="cosmic-context-menu-divider"></div>
        <button class="cosmic-context-menu-item" data-action="top"><i class="fas fa-rocket"></i> Back to Top</button>
        <button class="cosmic-context-menu-item" data-action="back"><i class="fas fa-arrow-left"></i> Go Back</button>
        <button class="cosmic-context-menu-item" data-action="reload"><i class="fas fa-sync-alt"></i> Reload</button>
    `;
    document.body.appendChild(contextMenu);

    document.addEventListener('contextmenu', (e) => {
        const chatContent = e.target.closest('.chat-message-content');
        if (chatContent && chatContent.dataset.messageId) return;
        e.preventDefault();
        contextMenu.classList.add('active');
        contextMenu.style.left = Math.min(e.clientX, window.innerWidth - 220) + 'px';
        contextMenu.style.top = Math.min(e.clientY, window.innerHeight - 280) + 'px';
    });

    document.addEventListener('click', (e) => {
        if (!contextMenu.contains(e.target)) contextMenu.classList.remove('active');
    });

    contextMenu.addEventListener('click', (e) => {
        const action = e.target.closest('.cosmic-context-menu-item')?.dataset.action;
        if (!action) return;
        contextMenu.classList.remove('active');
        switch(action) {
            case 'home': window.location.href = '/CosmicDiary/pages/home.php'; break;
            case 'gallery': window.location.href = '/CosmicDiary/pages/planet-gallery.php'; break;
            case 'logs': window.location.href = '/CosmicDiary/pages/captain-log.php'; break;
            case 'top': window.scrollTo({ top: 0, behavior: 'smooth' }); break;
            case 'back': window.history.back(); break;
            case 'reload': window.location.reload(); break;
        }
    });

    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.glass-nav ul li a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.style.color = 'var(--cyan)';
            link.style.textShadow = '0 0 10px var(--cyan)';
        }
    });

    const notifBadge = document.getElementById('notifBadge');
    if (notifBadge) {
        fetch('/CosmicDiary/api/user/notifications.php?action=count')
            .then(res => res.json())
            .then(data => {
                if (data.total > 0) {
                    notifBadge.textContent = data.total;
                    notifBadge.style.display = 'inline-block';
                }
            });
    }

    document.getElementById('confirmCancel').addEventListener('click', () => {
        document.getElementById('confirmModal').style.display = 'none';
    });

    const storyImageFile = document.getElementById('storyImageFile');
    const storyText = document.getElementById('storyText');
    const storyColor = document.getElementById('storyColor');

    if (storyImageFile) {
        storyImageFile.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('storyPreview');
                    if (preview) preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (storyText) {
        storyText.addEventListener('input', function() {
            const text = this.value;
            const color = document.getElementById('storyColor')?.value || '#7b2fff';
            const preview = document.getElementById('storyPreview');
            if (preview) {
                preview.innerHTML = `<div style="width:100%;min-height:100px;height:auto;max-height:300px;display:flex;align-items:flex-start;justify-content:center;font-size:1.2rem;color:white;padding:1.5rem;text-align:center;background:${color};border-radius:12px;word-wrap:break-word;overflow-wrap:break-word;word-break:break-word;white-space:normal;overflow-y:auto;line-height:1.6;">${text || 'Your story will appear here'}</div>`;
            }
        });
    }

    if (storyColor) {
        storyColor.addEventListener('input', function() {
            const text = document.getElementById('storyText')?.value || '';
            const preview = document.getElementById('storyPreview');
            if (preview) {
                preview.innerHTML = `<div style="width:100%;min-height:100px;height:auto;max-height:300px;display:flex;align-items:flex-start;justify-content:center;font-size:1.2rem;color:white;padding:1.5rem;text-align:center;background:${this.value};border-radius:12px;word-wrap:break-word;overflow-wrap:break-word;word-break:break-word;white-space:normal;overflow-y:auto;line-height:1.6;">${text || 'Your story will appear here'}</div>`;
            }
        });
    }
});

if (document.getElementById('chatButton')) {
    let chatLastId = 0;
    let chatOpen = false;
    let selectedMessageId = null;
    let selectedMessageText = null;
    let replyingTo = null;
    let mentionActive = false;
    let mentionQuery = '';
    let mentionIndex = 0;
    let mentionUsers = [];
    let mentionStartRange = null;
    let chatParticipants = [];

    const emojis = ['smile', 'wink', 'sad', 'cool', 'happy', 'angry', 'silly', 'neutral', 'joy', 'heart-eyes', 'disappointed', 'shy', 'sob', 'scream'];

    // Replaces emoji codes and @mentions with rendered HTML.
    function renderEmojis(text) {
        let html = text;
        emojis.forEach(e => {
            html = html.replaceAll(`:${e}:`, `<img src="/CosmicDiary/assets/images/emojis/${e}.png" class="chat-emoji" alt="${e}">`);
        });
        html = html.replace(/@(\w+)/g, '<span class="chat-mention" onclick="event.stopPropagation(); window.location.href=\'/CosmicDiary/pages/explorer.php?username=$1\'">@$1</span>');
        return html;
    }

    // Builds a local list of known chat participants.
    function loadChatParticipants() {
        const usernames = new Set();
        document.querySelectorAll('.chat-message-username').forEach(el => {
            usernames.add(el.textContent.trim());
        });
        chatParticipants = Array.from(usernames);
    }

    // Opens or closes the emoji picker panel.
    function toggleEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        if (!picker.classList.contains('open')) {
            picker.classList.add('open');
            picker.innerHTML = emojis.map(e => 
                `<img src="/CosmicDiary/assets/images/emojis/${e}.png" alt="${e}" onclick="insertEmoji('${e}')" title="${e}">`
            ).join('');
        } else {
            picker.classList.remove('open');
        }
    }
    
    // Inserts a selected emoji into the chat composer.
    function insertEmoji(name) {
        const input = document.getElementById('chatInput');
        const emojiImg = `<img src="/CosmicDiary/assets/images/emojis/${name}.png" class="chat-emoji" data-emoji=":${name}:" alt="${name}">`;
        input.focus();
        document.execCommand('insertHTML', false, emojiImg);
    }

    // Converts the rich chat input into plain message text.
    function extractMessageFromInput() {
        const input = document.getElementById('chatInput');
        let html = input.innerHTML;
        const imgs = input.querySelectorAll('img[data-emoji]');
        imgs.forEach(img => {
            html = html.replace(img.outerHTML, img.dataset.emoji);
        });
        return html.replace(/<br>/g, '').replace(/<div>/g, '').replace(/<\/div>/g, '').replace(/&nbsp;/g, ' ').trim();
    }

    const chatContextMenu = document.createElement('div');
    chatContextMenu.className = 'cosmic-context-menu';
    chatContextMenu.id = 'chatContextMenu';
    chatContextMenu.innerHTML = `
        <button class="cosmic-context-menu-item" data-action="copy"><i class="fas fa-copy"></i> Copy Text</button>
        <button class="cosmic-context-menu-item" data-action="reply"><i class="fas fa-reply"></i> Reply</button>
        <button class="cosmic-context-menu-item" data-action="edit"><i class="fas fa-pencil-alt"></i> Edit</button>
        <div class="cosmic-context-menu-divider"></div>
        <button class="cosmic-context-menu-item" data-action="delete"><i class="fas fa-trash-alt"></i> Delete</button>
    `;
    document.body.appendChild(chatContextMenu);

    document.addEventListener('contextmenu', (e) => {
        const messageContent = e.target.closest('.chat-message-content');
        if (messageContent && messageContent.dataset.messageId) {
            e.preventDefault();
            selectedMessageId = messageContent.dataset.messageId;
            selectedMessageText = messageContent.dataset.messageText;
            const msgDiv = messageContent.closest('.chat-message');
            const isMine = msgDiv && msgDiv.classList.contains('mine');
            const replyBtn = chatContextMenu.querySelector('[data-action="reply"]');
            const editBtn = chatContextMenu.querySelector('[data-action="edit"]');
            const deleteBtn = chatContextMenu.querySelector('[data-action="delete"]');
            if (replyBtn) replyBtn.style.display = 'flex';
            if (editBtn) editBtn.style.display = isMine ? 'flex' : 'none';
            if (deleteBtn) deleteBtn.style.display = isMine ? 'flex' : 'none';
            chatContextMenu.classList.add('active');
            chatContextMenu.style.left = Math.min(e.clientX, window.innerWidth - 220) + 'px';
            chatContextMenu.style.top = Math.min(e.clientY, window.innerHeight - 150) + 'px';
        } else {
            chatContextMenu.classList.remove('active');
        }
    });

    document.addEventListener('click', (e) => {
        if (!chatContextMenu.contains(e.target)) chatContextMenu.classList.remove('active');
    });

    chatContextMenu.addEventListener('click', (e) => {
        const action = e.target.closest('.cosmic-context-menu-item')?.dataset.action;
        if (!action || !selectedMessageId) return;
        chatContextMenu.classList.remove('active');

        switch(action) {
            case 'copy':
                navigator.clipboard.writeText(selectedMessageText).then(() => showToast('Message copied!', 'success'));
                break;
            case 'reply':
                replyingTo = {
                    id: selectedMessageId,
                    username: document.querySelector(`.chat-message-content[data-message-id="${selectedMessageId}"]`)?.closest('.chat-message')?.querySelector('.chat-message-username')?.textContent || 'User',
                    text: selectedMessageText
                };
                showReplyPreview();
                document.getElementById('chatInput').focus();
                break;

            case 'edit':
                const msgDiv = document.querySelector(`.chat-message-content[data-message-id="${selectedMessageId}"]`);
                if (!msgDiv) return;
                const textEl = msgDiv.querySelector('.chat-message-text');
                const originalHTML = textEl.innerHTML;
                const input = document.createElement('input');
                input.type = 'text';
                input.value = selectedMessageText;
                input.style.cssText = 'width:100%;padding:0.4rem;background:rgba(255,255,255,0.15);border:1px solid var(--cyan);border-radius:8px;color:white;font-family:inherit;font-size:0.85rem;outline:none;';
                textEl.replaceWith(input);
                input.focus();
                input.select();
                // Submits an edited chat message back to the server.
                const saveEdit = () => {
                    const newMessage = input.value.trim();
                    if (newMessage && newMessage !== selectedMessageText) {
                        const formData = new FormData();
                        formData.append('id', selectedMessageId);
                        formData.append('message', newMessage);
                        fetch('/CosmicDiary/api/chat/edit.php', { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    const newTextEl = document.createElement('div');
                                    newTextEl.className = 'chat-message-text';
                                    newTextEl.innerHTML = renderEmojis(newMessage);
                                    input.replaceWith(newTextEl);
                                    const timeEl = msgDiv.querySelector('.chat-message-time');
                                    if (timeEl) timeEl.innerHTML = `${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} <i class="fas fa-pencil-alt" style="font-size:0.55rem;color:#b0b0ff;" title="Edited"></i>`;
                                } else {
                                    showToast(data.message, 'error');
                                    const fallbackEl = document.createElement('div');
                                    fallbackEl.className = 'chat-message-text';
                                    fallbackEl.innerHTML = originalHTML;
                                    input.replaceWith(fallbackEl);
                                }
                            });
                    } else {
                        const fallbackEl = document.createElement('div');
                        fallbackEl.className = 'chat-message-text';
                        fallbackEl.innerHTML = originalHTML;
                        input.replaceWith(fallbackEl);
                    }
                };
                input.addEventListener('blur', saveEdit);
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') input.blur();
                    if (e.key === 'Escape') { input.value = selectedMessageText; input.blur(); }
                });
                break;
            case 'delete':
                showConfirm('Delete Message', 'This message will be removed from the chat.', 'fa-trash-alt', () => {
                    const delFormData = new FormData();
                    delFormData.append('id', selectedMessageId);
                    fetch('/CosmicDiary/api/chat/delete.php', { method: 'POST', body: delFormData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const msgElement = document.querySelector(`.chat-message-content[data-message-id="${selectedMessageId}"]`);
                                if (msgElement) {
                                    const chatMessage = msgElement.closest('.chat-message');
                                    if (chatMessage) {
                                        chatMessage.style.opacity = '0';
                                        chatMessage.style.transform = 'translateX(20px)';
                                        chatMessage.style.transition = 'opacity 0.2s, transform 0.2s';
                                        setTimeout(() => chatMessage.remove(), 200);
                                    }
                                }
                                showToast('Message deleted!', 'success');
                            } else {
                                showToast(data.message, 'error');
                            }
                        });
                });
                break;
        }
    });

    // Opens or closes the global chat sidebar.
    function toggleChat() {
        chatOpen = !chatOpen;
        const sidebar = document.getElementById('chatSidebar');
        const rocketBtn = document.getElementById('rocketTop');
        const chatBtn = document.getElementById('chatButton');
        const isMobile = window.innerWidth <= 768;
        if (chatOpen) {
            sidebar.classList.add('open');
            loadChatMessages();
            setTimeout(() => {
                document.getElementById('chatInput').focus();
                loadChatParticipants();
            }, 500);
            if (isMobile) {
                document.body.style.overflow = 'hidden';
                if (rocketBtn) rocketBtn.classList.add('hidden-mobile');
                if (chatBtn) chatBtn.classList.add('hidden-mobile');
            }
        } else {
            sidebar.classList.remove('open');
            document.body.style.overflow = '';
            if (isMobile) {
                if (rocketBtn) rocketBtn.classList.remove('hidden-mobile');
                if (chatBtn) chatBtn.classList.remove('hidden-mobile');
            }
        }
    }

    document.getElementById('chatButton').addEventListener('click', toggleChat);

    // Fetches chat messages and renders them in the sidebar.
    function loadChatMessages() {
        fetch(`/CosmicDiary/api/chat/read.php?last_id=${chatLastId}`)
            .then(res => res.json())
            .then(messages => {
                const container = document.getElementById('chatMessages');
                if (messages.length === 0 && chatLastId === 0) {
                    container.innerHTML = '<p style="text-align:center;color:#b0b0ff;padding:2rem;">No messages yet. Say hello!</p>';
                    return;
                }
                if (chatLastId === 0) container.innerHTML = '';
                messages.forEach(msg => {
                    chatLastId = Math.max(chatLastId, msg.id);
                    if (document.querySelector(`.chat-message-content[data-message-id="${msg.id}"]`)) return;
                    if (msg.deleted == 1) return;
                    
                    const msgDate = new Date(msg.created_at).toDateString();
                    const allDateSeps = container.querySelectorAll('.chat-date-separator');
                    let lastSepDate = null;
                    if (allDateSeps.length > 0) {
                        lastSepDate = allDateSeps[allDateSeps.length - 1].dataset.date;
                    } else {
                        const lastMessage = container.querySelector('.chat-message:last-child');
                        if (lastMessage) {
                            const prevMsgId = lastMessage.querySelector('.chat-message-content')?.dataset.messageId;
                            if (prevMsgId) {
                                const prevMsgEl = document.querySelector(`.chat-message-content[data-message-id="${prevMsgId}"]`);
                                if (prevMsgEl) {
                                    const prevTime = prevMsgEl.querySelector('.chat-message-time');
                                    if (prevTime) lastSepDate = new Date(prevTime.textContent).toDateString();
                                }
                            }
                        }
                    }
                    
                    if (msgDate !== lastSepDate) {
                        const dateDiv = document.createElement('div');
                        dateDiv.className = 'chat-date-separator';
                        dateDiv.dataset.date = msgDate;
                        dateDiv.textContent = formatChatDate(new Date(msg.created_at));
                        container.appendChild(dateDiv);
                    }
                    
                    const div = document.createElement('div');
                    div.className = `chat-message ${msg.is_mine ? 'mine' : ''}`;
                    div.innerHTML = `
                        ${!msg.is_mine ? `<a href="/CosmicDiary/pages/explorer.php?id=${msg.user_id}" onclick="event.stopPropagation();"><img src="${msg.avatar_url}" alt="${msg.username}" style="cursor:pointer;" title="View ${msg.username}'s profile"></a>` : ''}
                        <div class="chat-message-content" data-message-id="${msg.id}" data-message-text="${msg.message.replace(/"/g, '&quot;')}">
                            ${!msg.is_mine ? `<div class="chat-message-username">${msg.username}</div>` : ''}
                            ${msg.reply_to ? `
                                <div class="chat-reply-bubble" onclick="scrollToMessage(${msg.reply_to})">
                                    <span class="chat-reply-username">${msg.reply_username}</span>
                                    <span class="chat-reply-text">${msg.reply_text ? renderEmojis(msg.reply_text.substring(0, 50)) : 'Message'}</span>
                                </div>
                            ` : ''}
                            <div class="chat-message-text">${renderEmojis(msg.message)}</div>
                            <div class="chat-message-time">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}${msg.edited == 1 ? ' <i class="fas fa-pencil-alt" style="font-size:0.55rem;color:#b0b0ff;" title="Edited"></i>' : ''}</div>
                        </div>
                    `;
                    container.appendChild(div);
                });
                if (messages.length > 0) container.scrollTop = container.scrollHeight;
            });
    }

    // Submits the current chat message to the server.
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const message = extractMessageFromInput();
        if (!message) return;
        const formData = new FormData();
        formData.append('message', message);
        
        if (replyingTo) {
            formData.append('reply_to', replyingTo.id);
            formData.append('reply_username', replyingTo.username);
            formData.append('reply_text', replyingTo.text);
        }
        
        fetch('/CosmicDiary/api/chat/send.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.innerHTML = '';
                    replyingTo = null;
                    cancelReply();
                    document.getElementById('emojiPicker').classList.remove('open');
                    
                    const mentions = message.match(/@(\w+)/g);
                    if (mentions) {
                        mentions.forEach(mention => {
                            const username = mention.substring(1);
                            fetch('/CosmicDiary/api/chat/notify-mention.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'username=' + encodeURIComponent(username) + '&message=' + encodeURIComponent(message)
                            });
                        });
                    }
                    
                    loadChatMessages();
                }
            });
    }

    // Formats chat timestamps as Today, Yesterday, or a date.
    function formatChatDate(date) {
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (date.toDateString() === today.toDateString()) return 'Today';
        if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return date.toLocaleDateString([], { weekday: 'long', month: 'short', day: 'numeric' });
    }

    document.getElementById('chatInput').addEventListener('keydown', (e) => {
        if (mentionActive) {
            const container = document.getElementById('mentionSuggestions');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                mentionIndex = (mentionIndex + 1) % mentionUsers.length;
                renderMentionList(mentionUsers);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                mentionIndex = (mentionIndex - 1 + mentionUsers.length) % mentionUsers.length;
                renderMentionList(mentionUsers);
            } else if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                if (mentionUsers[mentionIndex]) {
                    insertMention(mentionUsers[mentionIndex].username);
                }
            } else if (e.key === 'Escape') {
                container.classList.remove('active');
                mentionActive = false;
            }
            return;
        }
        
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    window.addEventListener('scroll', () => {
        const chatBtn = document.getElementById('chatButton');
        const chatSidebar = document.getElementById('chatSidebar');
        if (!chatBtn) return;
        const footer = document.querySelector('footer');
        if (!footer) return;
        const footerTop = footer.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        const isNearFooter = footerTop < windowHeight + 30;
        if (isNearFooter) {
            chatBtn.classList.add('shifted');
            if (chatSidebar) chatSidebar.classList.add('shifted');
        } else {
            chatBtn.classList.remove('shifted');
            if (chatSidebar) chatSidebar.classList.remove('shifted');
        }
    });

    setInterval(() => { if (chatOpen) { loadChatMessages(); checkMessageUpdates(); } }, 500);

    // Polls for edited or deleted chat messages.
    function checkMessageUpdates() {
        const messageElements = document.querySelectorAll('.chat-message-content[data-message-id]');
        if (messageElements.length === 0) return;
        const ids = Array.from(messageElements).map(el => el.dataset.messageId).join(',');
        if (!ids) return;
        fetch(`/CosmicDiary/api/chat/check-updates.php?ids=${ids}`)
            .then(res => res.json())
            .then(updates => {
                updates.forEach(msg => {
                    const el = document.querySelector(`.chat-message-content[data-message-id="${msg.id}"]`);
                    if (!el) return;
                    if (msg.deleted == 1) {
                        const chatMessage = el.closest('.chat-message');
                        if (chatMessage) {
                            const isMine = chatMessage.classList.contains('mine');
                            chatMessage.style.opacity = '0';
                            chatMessage.style.transform = isMine ? 'translateX(20px)' : 'translateX(-20px)';
                            chatMessage.style.transition = 'opacity 0.2s, transform 0.2s';
                            setTimeout(() => chatMessage.remove(), 200);
                        }
                        return;
                    }
                    const textEl = el.querySelector('.chat-message-text');
                    const timeEl = el.querySelector('.chat-message-time');
                    if (textEl) textEl.innerHTML = renderEmojis(msg.message);
                    if (timeEl) timeEl.innerHTML = `${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}${msg.edited == 1 ? ' <i class="fas fa-pencil-alt" style="font-size:0.55rem;color:#b0b0ff;" title="Edited"></i>' : ''}`;
                });
            }).catch(() => {});
    }

    // Displays the preview bar for the message being replied to.
    function showReplyPreview() {
        if (!replyingTo) return;
        document.getElementById('replyPreview').classList.add('active');
        document.getElementById('replyPreviewUsername').textContent = replyingTo.username;
        document.getElementById('replyPreviewText').innerHTML = renderEmojis(replyingTo.text.substring(0, 80));
    }

    // Clears the active reply target and hides the preview.
    function cancelReply() {
        replyingTo = null;
        document.getElementById('replyPreview').classList.remove('active');
    }

    // Returns the caret position inside the chat input.
    function getCaretPosition(element) {
        const selection = window.getSelection();
        if (selection.rangeCount === 0) return 0;
        const range = selection.getRangeAt(0);
        const preRange = range.cloneRange();
        preRange.selectNodeContents(element);
        preRange.setEnd(range.endContainer, range.endOffset);
        return preRange.toString().length;
    }

    // Shows user mention suggestions for the current query.
    function showMentionSuggestions() {
        const container = document.getElementById('mentionSuggestions');
        const filtered = chatParticipants.filter(u => u.toLowerCase().startsWith(mentionQuery));
        
        if (filtered.length === 0 && mentionQuery.length >= 2) {
            fetch(`/CosmicDiary/api/user/search.php?q=${encodeURIComponent(mentionQuery)}`)
                .then(res => res.json())
                .then(users => {
                    mentionUsers = users;
                    renderMentionList(users);
                });
        } else {
            mentionUsers = filtered.map(u => ({ username: u, avatar_url: null }));
            renderMentionList(mentionUsers);
        }
    }
    
    // Renders the mention suggestion dropdown.
    function renderMentionList(users) {
        const container = document.getElementById('mentionSuggestions');
        if (users.length === 0) {
            container.classList.remove('active');
            return;
        }
        container.classList.add('active');
        container.innerHTML = users.map((u, i) => `
            <div class="mention-suggestion-item ${i === mentionIndex ? 'active' : ''}" 
                 onmousedown="event.preventDefault(); insertMention('${u.username}')"
                 data-username="${u.username}">
                <img src="${u.avatar_url || '/CosmicDiary/uploads/avatars/default.png'}" alt="${u.username}">
                <span class="mention-suggestion-username">${u.username}</span>
            </div>
        `).join('');
    }
    
    // Inserts a chosen mention at the current cursor position.
    function insertMention(username) {
        const input = document.getElementById('chatInput');
        const sel = window.getSelection();
        if (sel.rangeCount === 0) return;
        
        const range = sel.getRangeAt(0);
        const preRange = range.cloneRange();
        preRange.selectNodeContents(input);
        preRange.setEnd(range.endContainer, range.endOffset);
        const cursorPos = preRange.toString().length;
        
        const plainText = input.innerText;
        const textBeforeCursor = plainText.substring(0, cursorPos);
        const atPos = textBeforeCursor.lastIndexOf('@');
        
        if (atPos === -1) {
            document.getElementById('mentionSuggestions').classList.remove('active');
            mentionActive = false;
            return;
        }
        
        const beforeAt = plainText.substring(0, atPos);
        const afterCursor = plainText.substring(cursorPos);
        
        const mentionNode = document.createTextNode('@' + username + ' ');
        const afterNode = document.createTextNode(afterCursor);
        
        let deleteCount = cursorPos - atPos;
        let currentRange = document.createRange();
        currentRange.selectNodeContents(input);
        currentRange.collapse(true);
        
        let walker = document.createTreeWalker(input, NodeFilter.SHOW_TEXT);
        let node;
        let charCount = 0;
        let startNode = null;
        let startOffset = 0;
        
        while (node = walker.nextNode()) {
            const nodeLength = node.textContent.length;
            if (charCount + nodeLength >= atPos && !startNode) {
                startNode = node;
                startOffset = atPos - charCount;
            }
            if (startNode && charCount + nodeLength >= cursorPos) {
                const endOffset = cursorPos - charCount;
                
                if (startNode === node) {
                    const originalText = startNode.textContent;
                    startNode.textContent = originalText.substring(0, startOffset);
                    
                    const endText = originalText.substring(endOffset);
                    const nextNode = startNode.nextSibling;
                    
                    startNode.parentNode.insertBefore(mentionNode, nextNode);
                    
                    if (endText) {
                        const endTextNode = document.createTextNode(endText);
                        startNode.parentNode.insertBefore(endTextNode, mentionNode.nextSibling);
                        const newRange = document.createRange();
                        newRange.setStart(endTextNode, 0);
                        newRange.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(newRange);
                    } else {
                        const newRange = document.createRange();
                        newRange.setStart(mentionNode, mentionNode.textContent.length);
                        newRange.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(newRange);
                    }
                }
                break;
            }
            charCount += nodeLength;
        }
        
        document.getElementById('mentionSuggestions').classList.remove('active');
        mentionActive = false;
        input.focus();
    }

    document.getElementById('chatInput').addEventListener('input', function(e) {
        const text = this.textContent || this.innerText;
        const sel = window.getSelection();
        if (sel.rangeCount === 0) return;
        const range = sel.getRangeAt(0);
        const preRange = range.cloneRange();
        preRange.selectNodeContents(this);
        preRange.setEnd(range.endContainer, range.endOffset);
        const cursorPos = preRange.toString().length;
        const textBeforeCursor = text.substring(0, cursorPos);
        const atMatch = textBeforeCursor.match(/@(\w*)$/);
        
        if (atMatch) {
            mentionActive = true;
            mentionQuery = atMatch[1].toLowerCase();
            mentionIndex = 0;
            mentionStartRange = range.cloneRange();
            showMentionSuggestions();
        } else {
            mentionActive = false;
            document.getElementById('mentionSuggestions').classList.remove('active');
        }
    });

    // Scrolls a chat message into view and highlights it.
    function scrollToMessage(msgId) {
        const msgEl = document.querySelector(`.chat-message-content[data-message-id="${msgId}"]`);
        if (msgEl) {
            msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            msgEl.style.boxShadow = '0 0 10px var(--cyan)';
            setTimeout(() => msgEl.style.boxShadow = '', 1500);
        }
    }

}

let storyType = 'text';

// Opens the story creation modal.
function showStoryCreator() {
    const modal = document.getElementById('storyModal');
    if (modal) {
        modal.style.display = 'flex';
        storyType = 'text';
        document.getElementById('storyTextInput').style.display = 'block';
        document.getElementById('storyImageInput').style.display = 'none';
        document.getElementById('storyPreview').innerHTML = '<p style="color:#b0b0ff;">Write your story</p>';
        document.getElementById('storyText').value = '';
    }
}

// Switches the story editor between text and image mode.
function pickStoryType(type) {
    storyType = type;
    if (type === 'text') {
        document.getElementById('storyTextInput').style.display = 'block';
        document.getElementById('storyImageInput').style.display = 'none';
        document.getElementById('storyPreview').innerHTML = '<p style="color:#b0b0ff;">Write your story</p>';
    } else {
        document.getElementById('storyTextInput').style.display = 'none';
        document.getElementById('storyImageInput').style.display = 'block';
        document.getElementById('storyPreview').innerHTML = '<p style="color:#b0b0ff;">Select an image</p>';
    }
}

// Uploads the currently drafted story.
function submitStory() {
    const formData = new FormData();
    formData.append('type', storyType);
    if (storyType === 'text') {
        const text = document.getElementById('storyText').value.trim();
        if (!text) { showToast('Write something!', 'error'); return; }
        formData.append('content', text);
        formData.append('color', document.getElementById('storyColor').value);
    } else {
        const file = document.getElementById('storyImageFile').files[0];
        if (!file) { showToast('Select an image!', 'error'); return; }
        formData.append('image', file);
        formData.append('color', '#7b2fff');
        formData.append('content', 'image');
    }
    fetch('/CosmicDiary/api/stories/create.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.success) { showToast('Story posted!', 'success'); closeStoryModal(); loadStories(); }
            else showToast(data.message || 'Failed to post story.', 'error');
        });
}

// Closes the story creation modal.
function closeStoryModal() {
    const modal = document.getElementById('storyModal');
    if (modal) modal.style.display = 'none';
}

// Loads all stories and groups them by author.
function loadStories() {
    const container = document.getElementById('storiesContainer');
    if (!container) return;
    fetch('/CosmicDiary/api/stories/read.php')
        .then(res => res.json())
        .then(stories => {
            const grouped = {};
            stories.forEach(s => { if (!grouped[s.user_id]) grouped[s.user_id] = []; grouped[s.user_id].push(s); });
            let html = '';
            const myAvatar = stories.length > 0 ? (stories.find(s => s.is_mine)?.avatar_url || '/CosmicDiary/uploads/avatars/default.png') : '/CosmicDiary/uploads/avatars/default.png';
            html += `<div class="story-circle my-story" onclick="showStoryCreator()" title="Add Story"><img src="${myAvatar}" style="opacity:0.7;"><div class="story-plus"><i class="fas fa-plus"></i></div></div>`;
            for (const [userId, userStories] of Object.entries(grouped)) {
                const latest = userStories[0];
                html += `<div class="story-circle has-story" onclick="viewStories(${userId})" title="${latest.username} (${userStories.length} stories)"><img src="${latest.avatar_url}" alt="${latest.username}">${userStories.length > 1 ? `<span style="position:absolute;bottom:-2px;left:50%;transform:translateX(-50%);background:#00f7ff;color:#0a0a1a;font-size:0.6rem;padding:1px 6px;border-radius:8px;font-family:'Space Mono';">${userStories.length}</span>` : ''}</div>`;
            }
            container.innerHTML = html || '<p style="color:#b0b0ff;text-align:center;padding:1rem;">No stories yet. Be the first!</p>';
        });
}

let currentStoryIndex = 0;
let currentStories = [];
let storyTimeout = null;

// Opens the story viewer for a selected user.
function viewStories(userId) {
    fetch('/CosmicDiary/api/stories/read.php')
        .then(res => res.json())
        .then(stories => {
            currentStories = stories.filter(s => s.user_id == userId);
            currentStoryIndex = 0;
            if (currentStories.length === 0) return;
            if (document.getElementById('storyViewer')) document.getElementById('storyViewer').remove();
            const progressChunks = currentStories.map((_, i) => `<div class="story-progress-chunk"><div class="story-progress-chunk-fill" id="progressFill${i}"></div></div>`).join('');
            const viewer = document.createElement('div');
            viewer.className = 'story-viewer open';
            viewer.id = 'storyViewer';
            viewer.innerHTML = `
                <div class="story-progress" id="storyProgressBar">${progressChunks}</div>
                <button class="story-viewer-close" onclick="closeStoryViewer()">&times;</button>
                ${currentStories[0].is_mine ? `<button class="story-viewer-delete" onclick="deleteCurrentStory()"><i class="fas fa-trash-alt"></i></button>` : ''}
                <div class="story-viewer-user"><img src="${currentStories[0].avatar_url}"><span>${currentStories[0].username}</span></div>
                <div class="story-tap-left" onclick="prevStory()"></div>
                <div class="story-tap-right" onclick="nextStory()"></div>
                <div class="story-viewer-content" id="storyContent"></div>
                <div class="story-views" id="storyViews"><i class="fas fa-eye"></i> <span id="storyViewsCount">${currentStories[0].views || 0}</span></div>
            `;
            document.body.appendChild(viewer);
            showCurrentStory();
        });
}

// Renders the current story in the viewer.
function showCurrentStory() {
    const story = currentStories[currentStoryIndex];
    if (!story) { closeStoryViewer(); return; }
    const content = document.getElementById('storyContent');
    const deleteBtn = document.querySelector('.story-viewer-delete');
    if (deleteBtn) deleteBtn.style.display = story.is_mine ? 'block' : 'none';
    if (story.type === 'image') {
        content.className = 'story-viewer-content image-story';
        content.style.background = 'transparent';
        content.innerHTML = `<img src="${story.image_url}" alt="Story">`;
    } else {
        content.className = 'story-viewer-content text-story';
        content.style.background = story.color || '#7b2fff';
        content.textContent = story.content;
    }
    document.getElementById('storyViewsCount').textContent = story.views || 0;
    const formData = new FormData();
    formData.append('story_id', story.id);
    fetch('/CosmicDiary/api/stories/view.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => { if (data.success) document.getElementById('storyViewsCount').textContent = data.views; });
    currentStories.forEach((_, i) => {
        const fill = document.getElementById(`progressFill${i}`);
        if (fill) {
            if (i < currentStoryIndex) { fill.classList.add('done'); fill.style.transition = 'none'; }
            else if (i === currentStoryIndex) {
                fill.classList.remove('done'); fill.style.width = '0%'; fill.style.transition = 'none';
                requestAnimationFrame(() => { requestAnimationFrame(() => { fill.style.width = '100%'; fill.style.transition = 'width 5s linear'; }); });
            } else { fill.classList.remove('done'); fill.style.width = '0%'; fill.style.transition = 'none'; }
        }
    });
    if (storyTimeout) clearTimeout(storyTimeout);
    storyTimeout = setTimeout(() => {
        if (currentStoryIndex < currentStories.length - 1) { currentStoryIndex++; showCurrentStory(); }
        else closeStoryViewer();
    }, 5000);
}

// Advances to the next story in the sequence.
function nextStory() { if (storyTimeout) clearTimeout(storyTimeout); if (currentStoryIndex < currentStories.length - 1) { currentStoryIndex++; showCurrentStory(); } else closeStoryViewer(); }
// Moves back to the previous story in the sequence.
function prevStory() { if (storyTimeout) clearTimeout(storyTimeout); if (currentStoryIndex > 0) { currentStoryIndex--; showCurrentStory(); } }
// Closes the story viewer and clears its state.
function closeStoryViewer() { if (storyTimeout) clearTimeout(storyTimeout); const viewer = document.getElementById('storyViewer'); if (viewer) viewer.remove(); currentStories = []; currentStoryIndex = 0; }

// Deletes the story currently shown in the viewer.
function deleteCurrentStory() {
    const story = currentStories[currentStoryIndex];
    if (!story) return;
    showConfirm('Delete Story', 'This story will be permanently removed.', 'fa-trash-alt', () => {
        const formData = new FormData();
        formData.append('id', story.id);
        fetch('/CosmicDiary/api/stories/delete.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Story deleted!', 'success');
                    currentStories.splice(currentStoryIndex, 1);
                    if (currentStories.length === 0) closeStoryViewer();
                    else { if (currentStoryIndex >= currentStories.length) currentStoryIndex = currentStories.length - 1; showCurrentStory(); }
                    loadStories();
                }
            });
    });
}

if (document.getElementById('storiesContainer')) loadStories();

// Triggers the success celebration animation.
function celebrate() {
    const colors = ['#ff00c8', '#00f7ff', '#7b2fff', '#00ff88', '#ffcc00'];
    const container = document.body;
    
    for (let i = 0; i < 60; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                top: -10px;
                left: ${Math.random() * 100}%;
                width: 8px;
                height: 8px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                z-index: 99999;
                pointer-events: none;
                animation: confettiFall ${1.5 + Math.random() * 2}s ease-in forwards;
                animation-delay: ${Math.random() * 0.5}s;
            `;
            container.appendChild(confetti);
            setTimeout(() => confetti.remove(), 4000);
        }, i * 20);
    }
}