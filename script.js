const survey_buttons = document.querySelectorAll('.to-survey');
const back_buttons = document.querySelectorAll('.back-button');
const checkall = document.querySelector('.check-all')
const checks = document.querySelectorAll('.check');
const inputs_color = document.querySelectorAll('.input-color');
const modals = document.querySelectorAll('.modal-content');
let points = 0;

// Przycisk biorę udział w ankiecie
survey_buttons.forEach(button => {
    button.addEventListener('click', ()=>{
        const container = document.querySelector('.container');
        const container_modals = document.querySelector('.container-modal');
        container.classList.add('d-none');
        container_modals.classList.remove('d-none');
    })    
});

// Przycisk do nastepnej strony
function button_click(number) {
    const inputs = document.querySelectorAll(`.input-answer-${number}`);
    const alerts = document.querySelector(`.alert-${number}`);

    inputs.forEach(input => {
        if(input.classList.contains('border-click')) {
            let temp = 0;
            for(let i = 0; i < modals.length; i++) {
                if(!modals[i].classList.contains('d-none') && temp === 0) {
                    modals[i].classList.add('d-none')
                    modals[i + 1].classList.remove('d-none')
                    temp = 1;
                }
            }
        }
    });

    if(!inputs[0].classList.contains('border-click') && !inputs[1].classList.contains('border-click') && !inputs[2].classList.contains('border-click')) {
        alerts.style.visibility = 'visible';
    } else {
        alerts.style.visibility = 'hidden';
        
        points = (number == 'one' && inputs[2].classList.contains('border-click')) ? 1 : (number == 'one' ? 0 : points);

        points = (number == 'two' && inputs[1].classList.contains('border-click')) ? (points >= 1 ? 2 : 1) : (number == 'two' ? (points >= 1 ? 1 : 0) : points);
        
        points = (number == 'three' && inputs[0].classList.contains('border-click')) ? (points >= 2 ? 3 : points == 1 ? 2 : 1) : (number === 'three' ? (points >= 2 ? 2 : points >= 1 ? 1 : 0) : points);
        
    }
    span = document.querySelector('.final-result').innerText = `${points}/3`;
}

// Kolory do wszystkich odpowiedzi
for(let i = 0; i < inputs_color.length ;i++){
    inputs_color[i].addEventListener('click', () => {
        change_color(i <= 2 ? 'one' : (i <= 5 ? 'two' : 'three'));

        function change_color(number){
            const inputs = document.querySelectorAll(`.input-answer-${number}`);
            inputs.forEach(input => {
                    if (input.classList.contains('border-click')) {
                        input.classList.remove('border-click');
                        input.parentElement.firstElementChild.src = "./media/flower-icon.svg";
                        input.previousElementSibling.style.color = '#FFFFFF';
                    }
            });
            inputs_color[i].classList.toggle('border-click');
            inputs_color[i].parentElement.firstElementChild.src = "./media/flower-icon-color.svg";
            inputs_color[i].previousElementSibling.style.color = '#543E3C';
        }
    });
}

// Przycisk poprzednie pytanie
back_buttons.forEach(button => {
    button.addEventListener('click', ()=>{
        let temp = 0;
        for(let i = 0; i < modals.length; i++){
            if(!modals[i].classList.contains('d-none') && temp === 0){
                modals[i].classList.add('d-none');
                modals[i - 1].classList.remove('d-none');
                temp = 1;
            }
        }
    })    
});

// Zaznaczenie wszystkich zgód
checkall.addEventListener('click', () =>{
    checks.forEach(check => {
        if(checkall.checked === true){
            check.checked = true;
        } else if(checkall.checked === false){
            check.checked = false;
        }
    });
})

// Przycisk submit ankiety
document.getElementById("survey-form").addEventListener("submit", function(event) {
    event.preventDefault();
            let temp = 0;
            for(let i = 0; i < modals.length; i++) {
                if(!modals[i].classList.contains('d-none') && temp === 0) {
                    modals[i].classList.add('d-none')
                    modals[i + 1].classList.remove('d-none')
                    temp = 1;
                }
            }
});