let fontMode = 'Elvish',
    opFontMode = {
        'Elvish': 'Modern',
        'Modern': 'Elvish'
    },
    toggle = document.querySelector('#toggle-font'),
    mainMsgElvish = document.querySelector('#main-message-elvish'),
    subMsgElvish = document.querySelector('#sub-message-elvish'),
    mainMsgModern = document.querySelector('#main-message-modern'),
    subMsgModern = document.querySelector('#sub-message-modern');

toggle.addEventListener('click', toggleFont);

// Mountains
function toggleFont()
{
    mainMsgElvish.classList.toggle('hidden');
    subMsgElvish.classList.toggle('hidden');
    mainMsgModern.classList.toggle('hidden');
    subMsgModern.classList.toggle('hidden');
    toggle.innerHTML = fontMode;
    fontMode = opFontMode[fontMode];
}
