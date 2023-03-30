'use strict';

const chat = document.querySelector('.chat');
const openButton = document.querySelector('.chat-open');
const closeButton = document.querySelector('.chat-exit');

const toggleChat = () => {
    chat.classList.toggle('chat-closed');
    openButton.classList.toggle('chat-closed');
};

openButton.addEventListener('click', toggleChat);
closeButton.addEventListener('click', toggleChat);