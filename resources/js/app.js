import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//Settings > Play Button
if (document.getElementById('arrondiplay') && document.getElementById('formgame')) {
    document.getElementById('arrondiplay').addEventListener("click",
        function () {
            document.getElementById('formgame').submit()
        });
}

