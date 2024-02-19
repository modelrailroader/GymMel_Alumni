export const easterEggs = () => {
    const name = document.getElementById('name');
    const email = document.getElementById('email')
    if (name && email) {
        name.addEventListener('keyup', function() {
            if (name.value === 'Biene Maja') {
                email.value = 'Majaaaaa!';
            }
            if (name.value === 'Albert Einstein') {
                email.value = '"Auf Veränderungen zu hoffen, ohne selbst etwas dafür zu tun, ist wie am Bahnhof zu stehen und auf ein Schiff zu warten"';
            }
            if (name.value === 'Albus Dumbledore') {
                email.value = '"Es sind nicht unsere Fähigkeiten, die zeigen, wer wirklich sind, sondern die Entscheidungen, die wir treffen."'
            }
            if (name.value === 'Jane Godall') {
                email.value = '"Man kann keinen Tag durchleben, ohne dass es Auswirkungen auf die Welt hat. Und wir alle haben die Wahl, welche Art von Auswirkungen das sind."'
            }
            if (name.value === 'Margot Friedländer') {
                email.value = '"Ich bin gekommen, um euch die Hand zu reichen. Seid Menschen!"';
            }
        });
    }
}