if (localStorage.getItem('cookieSeen') !== 'yes')
{
    document.getElementsByClassName('cookie-banner')[0].style.display = 'flex';
}
function closeCookies()
{
    document.getElementsByClassName('cookie-banner')[0].style.display = 'none';
    localStorage.setItem('cookieSeen', 'yes');
}