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

function switchInformations(){
    let all = document.querySelector('.all');
    let winners = document.querySelector('.winners');

    all.classList.toggle('none');
    winners.classList.toggle('none');
}
