const czas = document.querySelector('.czas');

setInterval(() => {
    const now = new Date();
    const hour = now.getHours().toString().padStart(2, '0');
    const minute = now.getMinutes().toString().padStart(2, '0');
    const second = now.getSeconds().toString().padStart(2, '0');
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const timeString = `Czas: ${hour}:${minute}:${second} ${now.getDate()}.${month}.${now.getFullYear()}`;

    czas.innerHTML = timeString;
}, 1000);
setInterval(() => {
    const sukadziwkakurwa = document.getElementById('sukadziwkakurwa');

    const date = new Date();
    const day = date.getDate() < 10 ? `0${date.getDate()}` : date.getDate();
    const month = (date.getMonth()+1) < 10 ? `0${date.getMonth()+1}` : date.getMonth()+1;
    const year = date.getFullYear();
    sukadziwkakurwa.innerHTML = `${day}.${month}.${year}`;
}, 1000);