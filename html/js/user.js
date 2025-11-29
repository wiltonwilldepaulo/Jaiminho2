import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";

const InsertButton = document.getElementById('insert');

$('#cpf').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });

async function insert() {
    //Valida todos os campos do formulário
    const IsValid = Validate
        .SetForm('form')//Inform o ID do form
        .Validate();//Aplica a validação no campos 
    if (!IsValid) {
        Swal.fire({
            icon: "error",
            title: "Por favor preencha corretamente os campos!",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        //Em caso de erro encerramos o processo.
        return;
    }
    const response = await Requests.SetForm('form').Post('/usuario/insert');
    if (!response.status) {
        console.log(response);
        Swal.fire({
            icon: "error",
            title: response.msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        return;
    }
    document.getElementById('acao').value = 'e';
    //Modifica a URL da aplicação sem recarregar
    history.pushState(`/usuario/alterar/${response.id}`, '');
    Swal.fire({
        icon: "success",
        title: response.msg,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}
async function update() {
    //Valida todos os campos do formulário
    const IsValid = Validate
        .SetForm('form')//Inform o ID do form
        .Validate();//Aplica a validação no campos 
    if (!IsValid) {
        Swal.fire({
            icon: "error",
            title: "Por favor preencha corretamente os campos!",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        //Em caso de erro encerramos o processo.
        return;
    }
    const response = await Requests.SetForm('form').Post('/usuario/update');
    if (!response.status) {
        console.log(response);
        Swal.fire({
            icon: "error",
            title: response.msg,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        return;
    }
    Swal.fire({
        icon: "success",
        title: response.msg,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

InsertButton.addEventListener('click', async () => {
    const Action = document.getElementById('acao').value;
    (Action === 'c') ? await insert() : await update();
});
