const wrapped = document.querySelectorAll('.wrapped');

wrapped.forEach(element => {
    element.addEventListener('click', () =>{
        element.classList.toggle('unwrapped');
        element.closest('tr').nextElementSibling.classList.toggle('none');
    })
});

function submitForm() {
    document.getElementById("pages").submit();
}

function submitFormWinners() {
    document.getElementById("pagesWinners").submit();
}

window.onload = function() {
    let currentPage = localStorage.getItem("currentPage");

    if (currentPage === "winners") {
        switchInformations('winners');
    }
};

function switchInformations(section) {
    let all = document.querySelector('.all');
    let winners = document.querySelector('.winners');
    let winnersButton = document.getElementById('winnersButton');

    if (section === 'all') {
        all.classList.remove('none');
        winners.classList.add('none');
        winnersButton.classList.remove('activated-button');
        winnersButton.classList.add('disabled-button');
    } else if (section === 'winners') {
        all.classList.add('none');
        winners.classList.remove('none');
        winnersButton.classList.remove('disabled-button');
        winnersButton.classList.add('activated-button');
    }

    localStorage.setItem("currentPage", section);
}