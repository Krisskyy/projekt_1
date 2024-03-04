const questionnaire = document.getElementById("questionnaire");

const data = {
    userData: {
    npwz: "",
    pwzf: null,
    email: "",
    mainSpecialization: 0,
    subSpecializations: 0,
    name: "",
    surname: "",
    loggedInCount: 0,
    status: 0,
    id: 0,
    crm_id: 0,
    },
    message: {
    header: '',
    content: '',
    },
    version: "",
    };

    const {userData} = data;
    const {name, surname, id, npwz} = userData;
    const params = {
        name,
        surname,
        pharmaPassID: id,
        npwz,
        questionnaireId: "",

    }

    questionnaire.setAttribute("props",  JSON.stringify(params))