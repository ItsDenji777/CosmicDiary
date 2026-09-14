</main>
<footer>
    <?php
/**
 * Shared footer markup and closing layout wrapper for every page.
 */
    $quotesJson = file_get_contents(__DIR__ . '/../assets/data/quotes.json');
    $quotes = json_decode($quotesJson, true);
    $random = $quotes[array_rand($quotes)];
    ?>
    <p style="margin-bottom:0.5rem;font-style:italic;">"<?= htmlspecialchars($random['text']) ?>"</p>
    <p style="font-size:0.8rem;color:#b0b0ff;">— <?= htmlspecialchars($random['author']) ?></p>
    <p style="margin-top:1rem;">&copy; 2188 Cosmic Diary - Your Cosmic Stardate <?= date('Y') ?></p>
</footer>
<button id="rocketTop" class="rocket-top-btn" title="Back to top">
    <i><img src="../assets/images/rocketbtn.svg"></i></button>
</button>

<?php if (isLoggedIn()): ?>
<div id="chatButton" class="chat-button">
    <i class="fas fa-comments"></i>
    <span id="chatBadge" class="chat-badge" style="display:none;">0</span>
</div>

<div id="chatSidebar" class="chat-sidebar">
    <div class="chat-header">
        <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin:0;">Global Chat</h3>
        <button onclick="toggleChat()" style="background:none;border:none;color:white;cursor:pointer;font-size:1.2rem;"><i class="fas fa-times"></i></button>
    </div>
    <div class="chat-messages" id="chatMessages">
        <p style="text-align:center;color:#b0b0ff;padding:2rem;">Loading...</p>
    </div>
    <div class="reply-preview" id="replyPreview">
        <span class="reply-preview-username" id="replyPreviewUsername"></span>
        <span class="reply-preview-text" id="replyPreviewText"></span>
        <button class="reply-preview-close" onclick="cancelReply()"><i class="fas fa-times"></i></button>
    </div>
    <div class="mention-suggestions" id="mentionSuggestions"></div>
    <div class="chat-input-area">
        <div id="chatInput" contenteditable="true" placeholder="Type a message..." style="flex:1;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:30px;color:white;font-family:inherit;outline:none;min-height:42px;max-height:100px;overflow-y:auto;word-wrap:break-word;"></div>
        <button onclick="toggleEmojiPicker()" class="chat-btn-emoji" title="Emojis"><i class="far fa-smile"></i></button>
        <button onclick="sendMessage()" class="chat-btn-send" title="Send Message"><i class="fas fa-paper-plane"></i></button>
    </div>
    <div class="emoji-picker" id="emojiPicker"></div>
</div>
<div id="storyModal" class="story-modal" style="display:none;">
    <div class="story-modal-content glass-card" style="max-width:400px;">
        <h3 style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:1rem;">Create Story</h3>
        <div id="storyPreview" style="width:100%;height:300px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.05);margin-bottom:1rem;overflow:hidden;">
            <p style="color:#b0b0ff;">Your story will appear here</p>
        </div>
        <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
            <button onclick="pickStoryType('text')" class="btn-edit" style="flex:1;">Text</button>
            <button onclick="pickStoryType('image')" class="btn-edit" style="flex:1;">Image</button>
        </div>
        <div id="storyTextInput" style="display:none;">
            <textarea id="storyText" placeholder="Write your story..." rows="4" style="width:100%;padding:0.75rem;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;font-family:inherit;resize:vertical;margin-bottom:0.5rem;"></textarea>
            <div class="form-group">
                <label for="storyColor">Background Color</label>
                <input type="color" id="storyColor" value="#7b2fff" style="width:100%;height:40px;">
            </div>
        </div>
        <div id="storyImageInput" style="display:none;">
            <input type="file" id="storyImageFile" accept="image/*" style="margin-bottom:0.5rem;color:white;">
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button onclick="submitStory()" class="btn-primary" style="flex:1;">Post Story</button>
            <button onclick="closeStoryModal()" class="btn-delete" style="flex:1;width:auto;">Cancel</button>
        </div>
    </div>
</div>
<div id="confirmModal" class="story-modal" style="display:none;">
    <div class="story-modal-content glass-card" style="max-width:380px;text-align:center;">
        <i id="confirmIcon" class="fas fa-exclamation-triangle" style="font-size:2rem;color:#ffcc00;margin-bottom:1rem;display:block;"></i>
        <h3 id="confirmTitle" style="font-family:'Orbitron',sans-serif;color:#00f7ff;margin-bottom:0.5rem;">Are you sure?</h3>
        <p id="confirmMessage" style="color:#b0b0ff;margin-bottom:1.5rem;">This action cannot be undone.</p>
        <div style="display:flex;gap:0.5rem;">
            <button id="confirmCancel" class="btn-delete" style="flex:1;width:auto;background:rgba(255,255,255,0.1);">Cancel</button>
            <button id="confirmOk" class="btn-primary" style="flex:1;">Confirm</button>
        </div>
    </div>
</div>
<?php endif; ?>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>