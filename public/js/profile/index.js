Application.modules.profileIndex = (function(self) {
    var
        _loadConversationsList = function() {
            var privateKey = sessionStorage.getItem('private_key');
            var $conversationList = $('#conversations_list');
            if (!privateKey) {
                $conversationList.html('You should load a private key');
                return;
            }

            var conversationsRaw = $('#conversations').html();
            var conversations = JSON.parse(conversationsRaw);
            var resultConversations = [];
            for (var i = 0; i < conversations.length; i++) {
                var conversation = conversations[i];
                var key = ninjaCrypto.decryptRsa(conversation.key, privateKey);
                var theme = ninjaCrypto.decryptTripleDES(conversation.theme, key);
                resultConversations.push({
                    'id': conversation.id,
                    'theme': theme
                });
            }

            var list = '';
            $.each(resultConversations, function(key, item) {
                list += '<a href="/conversation/' + item.id + '">' + item.theme + '</a><br>';
            });
            $conversationList.html(list);
        }
        ;

    self.load = function() {
        _loadConversationsList();
    };

    return self;
}({}));
