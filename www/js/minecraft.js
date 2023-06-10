const pCounts = document.querySelectorAll('.minecraft-player-count');

async function fetchMinecraftServerData(ip) {
    try {
        const response = await fetch('https://api.mcsrvstat.us/2/' + MINECRAFT_SERVER_IP);
        return await response.json();
    } catch(error) {
        console.log(error);
        return undefined;
    }
}

async function fetchMinecraftPlayers() {
    const data = await fetchMinecraftServerData(MINECRAFT_SERVER_IP);
    if(!data) return;
    const { debug, online, players } = data;

    let text = '';

    if (!online) {
        text = 'Server je offline.';
    } else {
        let playerCount = players?.online;
        text = `Nyní hraj${playerCount >=2 && playerCount <= 4 ? 'í' : 'e'} ${playerCount !== undefined ? playerCount : '-'} hráč${playerCount === 1 ? '' : playerCount >=2 && playerCount <= 4 ? 'i' : 'ů'}`
    }

    pCounts.forEach(pCount => {
        pCount.textContent = text;
        if (!online)
            pCount.classList.add('text-danger');
        else
            pCount.classList.remove('text-danger');
    });

    const timeout = debug.cacheexpire * 1000 - new Date().getTime();

    setTimeout(fetchMinecraftPlayers, timeout > 0 ? timeout : 60_000);
}
$(document).ready(function()
{
    fetchMinecraftPlayers();
});