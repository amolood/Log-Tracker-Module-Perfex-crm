<p class="tw-font-semibold tw-text-lg">
    <?php echo _l('log_level_color_options') ?>
</p>
<div class="row">
    <div class="col-md-4">
        <?php echo render_color_picker('settings[date_color]', _l('date'), get_option('date_color')) ?>
    </div>
    <div class="col-md-4">
        <?php echo render_color_picker('settings[all_color]', _l('all'), get_option('all_color')) ?>
    </div>
    <div class="col-md-4">
        <?php echo render_color_picker('settings[critical_color]', _l('critical'), get_option('critical_color')) ?>
    </div>
    <div class="col-md-4">
        <?php echo render_color_picker('settings[error_color]', _l('error'), get_option('error_color')) ?>
    </div>
    <div class="col-md-4">
        <?php echo render_color_picker('settings[debug_color]', _l('debug'), get_option('debug_color')) ?>
    </div>
    <div class="col-md-4">
        <?php echo render_color_picker('settings[info_color]', _l('info'), get_option('info_color')) ?>
    </div>
</div>

<hr class="">
<p class="tw-font-semibold tw-text-lg">
    <?php echo _l('telegram_configuration') ?>
</p>
<div class="row">
    <div class="col-md-12">
        <?php render_yes_no_option('telegram_enabled', _l('enable_telegram_notifications'), '', _l('yes'), _l('no')); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?php echo render_input('settings[telegram_bot_token]', _l('telegram_bot_token'), get_option('telegram_bot_token'), 'text', ['placeholder' => '1234567890:AAFTEIQzaS58i1CBv5j0dHOlLkTcLpvhMO8']); ?>
    </div>
    <div class="col-md-6">
        <label for="telegram_chat_ids" class="control-label"><?php echo _l('telegram_chat_ids'); ?></label>
        <div id="chat_ids_tagsinput" class="form-control" style="min-height: 38px; display: flex; flex-wrap: wrap; align-items: center; padding: 2px 4px;">
            <!-- Tags will be rendered here -->
            <input type="text" id="telegram_chat_ids_input" style="border: none; outline: none; flex: 1; min-width: 120px;" placeholder="<?php echo _l('telegram_chat_id_placeholder'); ?>">
        </div>
        <input type="hidden" name="settings[telegram_chat_ids]" id="telegram_chat_ids_hidden" value="<?php echo get_option('telegram_chat_ids'); ?>">
        <small class="text-muted"><?php echo _l('telegram_chat_ids_help'); ?></small>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?php
        $levels = [
            ['id' => 'CRITICAL', 'name' => 'CRITICAL'],
            ['id' => 'ERROR', 'name' => 'ERROR'],
            ['id' => 'DEBUG', 'name' => 'DEBUG'],
            ['id' => 'INFO', 'name' => 'INFO'],
        ];
        $selected = explode(',', get_option('logtracker_auto_telegram_levels'));
        echo render_select('settings[logtracker_auto_telegram_levels]', $levels, ['id', 'name'], 'auto_send_telegram_notifications_on_levels', $selected, ['multiple' => true]);
        ?>
    </div>
    <div class="col-md-6">
        <label class="control-label d-block">&nbsp;</label>
        <button type="button" id="test_telegram" class="btn btn-info"><?php echo _l('test_telegram_message'); ?></button>
    </div>
</div>

<?php if (is_admin()) { ?>
    <hr class="">
    <p class="tw-font-semibold tw-text-lg">
        <?php echo _l('enviornment_mode') ?>
    </p>
    <div class="row">
        <div class="col-md-12">
            <?php render_yes_no_option('enviornment_mode', '', '', _l('development'), _l('production'), 'development', 'production'); ?>
        </div>
    </div>
    <?php echo displayEnvironmentMessage(); ?>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Tags Input for Telegram Chat IDs ---
    var tagsContainer = document.getElementById('chat_ids_tagsinput');
    var hiddenInput = document.getElementById('telegram_chat_ids_hidden');
    var input = document.getElementById('telegram_chat_ids_input');

    function renderTags() {
        // Clear existing tags before re-rendering
        Array.from(tagsContainer.querySelectorAll('.tag')).forEach(function(tag) {
            tag.remove();
        });
        var chatIds = hiddenInput.value ? hiddenInput.value.split(',').map(id => id.trim()).filter(Boolean) : [];
        chatIds.forEach(function(chatId) {
            var tag = document.createElement('span');
            tag.className = 'tag label label-info';
            tag.style.margin = '2px';
            tag.style.display = 'inline-block';
            tag.textContent = chatId;
            
            var removeBtn = document.createElement('span');
            removeBtn.textContent = ' ×';
            removeBtn.style.cursor = 'pointer';
            removeBtn.style.marginLeft = '5px';
            removeBtn.onclick = function() {
                removeTag(chatId);
            };
            
            tag.appendChild(removeBtn);
            tagsContainer.insertBefore(tag, input);
        });
    }

    function addTag(chatId) {
        var chatIds = hiddenInput.value ? hiddenInput.value.split(',').map(id => id.trim()).filter(Boolean) : [];
        if (chatId && chatIds.indexOf(chatId) === -1) {
            chatIds.push(chatId);
            hiddenInput.value = chatIds.join(',');
            renderTags();
        }
    }

    function removeTag(chatId) {
        var chatIds = hiddenInput.value ? hiddenInput.value.split(',').map(id => id.trim()).filter(Boolean) : [];
        var updatedChatIds = chatIds.filter(id => id !== chatId);
        hiddenInput.value = updatedChatIds.join(',');
        renderTags();
    }

    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',' || e.keyCode === 188) {
                e.preventDefault();
                var value = input.value.trim().replace(/,/g, '');
                if (value) {
                    addTag(value);
                    input.value = '';
                }
            }
        });
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    // --- Test Telegram Button ---
    var testButton = document.getElementById('test_telegram');
    if (testButton) {
        testButton.addEventListener('click', function() {
            this.disabled = true;
            
            var formData = new FormData();
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');

            fetch('<?php echo admin_url('logtracker/test_telegram'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Check the browser console.');
                this.disabled = false;
            });
        });
    }

    // Initial render of tags
    renderTags();
});
</script>