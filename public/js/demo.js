(function()
{
    let commonStyle = 'font-size: 12pt; font-family: Helvetica, Arial;',
        style1 = commonStyle + ' color: red;',
        style2 = commonStyle + ' color: royalblue;';

    let message1 = 'Welcome to Tarascanta!',
        message2 = "I'm Taras!";

    console.log(`%c${message1}\n%c${message2}!`, style1, style2);

    let expression = '17038\x20+\x2075\x20*\x204\x20-\x20340\x20/\x2017';
    console.log(eval(expression));
})();
