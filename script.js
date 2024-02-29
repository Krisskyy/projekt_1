let survey_buttons = document.querySelectorAll('.to_survey');
let inputs_one = document.querySelectorAll('.input_answer_one');
let inputs_two = document.querySelectorAll('.input_answer_two');
let inputs_three = document.querySelectorAll('.input_answer_three');
let next_button_one = document.querySelector('.next_question_one');
let next_button_two = document.querySelector('.next_question_two');
let next_button_three = document.querySelector('.next_question_three');
let back_buttons = document.querySelectorAll('.back_button');
let final_result = document.querySelector('.final_result');
let checkall = document.querySelector('.checkall')
let checks = document.querySelectorAll('.check');
let alert_one = document.querySelector('.alert_one');
let alert_two = document.querySelector('.alert_two');
let alert_three = document.querySelector('.alert_three');

let score = 0;

// Przycisk biorę udział w ankiecie
survey_buttons.forEach(button => {
    button.addEventListener('click', ()=>{
        let container = document.querySelector('.container');
        let container_modals = document.querySelector('.containerModal');
        container.classList.add('dNone');
        container_modals.classList.remove('dNone');
    })    
});

// Kolorowanie zaznaczonej odpowiedzi pierwszego zestawu pytan
inputs_one.forEach(input => {
    input.addEventListener('click', () => {
        inputs_one.forEach(input => {
            if(input.classList.contains('borderclick')){
                input.classList.remove('borderclick');
                input.parentElement.firstElementChild.src="./media/flower-icon.svg";
                input.previousElementSibling.style.color = '#FFFFFF'
            }
        });
        input.classList.toggle('borderclick');
        input.parentElement.firstElementChild.src="./media/flower-icon-color.svg";
        input.previousElementSibling.style.color = '#543E3C'
    });
});

// Przycisk nastepne pytanie pierwszego zestawu pytań
next_button_one.addEventListener('click', () =>{
    inputs_one.forEach(input => {

        if(input.classList.contains('borderclick')){
            let temp = 0;
            let modals = document.querySelectorAll('.modal-content');

            
            for(let i = 0; i < modals.length; i++){
                if(!modals[i].classList.contains('dNone') && temp === 0){
                    modals[i].classList.add('dNone');
                    modals[i + 1].classList.remove('dNone');
                    temp = 1;

                }
            }
        }   
    });

    if(!inputs_one[0].classList.contains('borderclick') && !inputs_one[1].classList.contains('borderclick') && !inputs_one[2].classList.contains('borderclick')) {
        alert_one.style.visibility = 'visible';
    } else{
        alert_one.style.visibility = 'hidden';
    }
})


// Kolorowanie zaznaczonej odpowiedzi drugiego zestawu pytan
inputs_two.forEach(input => {
    input.addEventListener('click', () => {
        inputs_two.forEach(input => {
            if(input.classList.contains('borderclick')){
                input.classList.remove('borderclick');
                input.parentElement.firstElementChild.src="./media/flower-icon.svg";
                input.previousElementSibling.style.color = '#FFFFFF'
            }
        });
        input.classList.toggle('borderclick');
        input.parentElement.firstElementChild.src="./media/flower-icon-color.svg";
        input.previousElementSibling.style.color = '#543E3C'
    });
});

// Przycisk nastepne pytanie drugiego zestawu pytań
next_button_two.addEventListener('click', () =>{
    inputs_two.forEach(input => {

        if(input.classList.contains('borderclick')){
            let temp = 0;
            let modals = document.querySelectorAll('.modal-content');

            
            for(let i = 0; i < modals.length; i++){
                if(!modals[i].classList.contains('dNone') && temp === 0){
                    modals[i].classList.add('dNone');
                    modals[i + 1].classList.remove('dNone');
                    temp = 1;
                }
            }
        }   
    });

    if(!inputs_two[0].classList.contains('borderclick') && !inputs_two[1].classList.contains('borderclick') && !inputs_two[2].classList.contains('borderclick')) {
        alert_two.style.visibility = 'visible';
    }else{
        alert_two.style.visibility = 'hidden';
    }
})

// Kolorowanie zaznaczonej odpowiedzi trzeciego zestawu pytan
inputs_three.forEach(input => {
    input.addEventListener('click', () => {
        inputs_three.forEach(input => {
            if(input.classList.contains('borderclick')){
                input.classList.remove('borderclick');
                input.parentElement.firstElementChild.src="./media/flower-icon.svg";
                input.previousElementSibling.style.color = '#FFFFFF'
            }
        });
        input.classList.toggle('borderclick');
        input.parentElement.firstElementChild.src="./media/flower-icon-color.svg";
        input.previousElementSibling.style.color = '#543E3C'
    });
});

// Przycisk nastepne pytanie trzeciego zestawu pytań
next_button_three.addEventListener('click', () =>{
    inputs_three.forEach(input => {

        if(input.classList.contains('borderclick')){
            let temp = 0;
            let modals = document.querySelectorAll('.modal-content');

            
            for(let i = 0; i < modals.length; i++){
                if(!modals[i].classList.contains('dNone') && temp === 0){
                    modals[i].classList.add('dNone');
                    modals[i + 1].classList.remove('dNone');
                    temp = 1;

                    alert_three.style.visibility = 'invisible';
                }
            }
        }   
    });

    if(!inputs_three[0].classList.contains('borderclick') && !inputs_three[1].classList.contains('borderclick') && !inputs_three[2].classList.contains('borderclick')) {
        alert_three.style.visibility = 'visible';
    } else{
        alert_three.style.visibility = 'hidden';
    }

    if(inputs_one[2].classList.contains('borderclick')){
        score++;
    }

    if(inputs_two[1].classList.contains('borderclick')){
        score++;
    }

    if(inputs_three[0].classList.contains('borderclick')){
        score++;
    }

    final_result.innerHTML = score + "/3";
})

// Przycisk poprzednie pytanie
back_buttons.forEach(button => {
    button.addEventListener('click', ()=>{
        let temp = 0;
        let modals = document.querySelectorAll('.modal-content');
        for(let i = 0; i < modals.length; i++){
            if(!modals[i].classList.contains('dNone') && temp === 0){
                modals[i].classList.add('dNone');
                modals[i - 1].classList.remove('dNone');
                temp = 1;
            }
        }
    })    
});

checkall.addEventListener('click', () =>{
    checks.forEach(check => {
        if(checkall.checked === true){
            check.checked = true;
        } else if(checkall.checked === false){
            check.checked = false;
        }
    });
})