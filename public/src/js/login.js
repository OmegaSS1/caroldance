import { api_base, api_mercado_pago } from './api.js'
import { mp } from './mercadopago.js'

$(document).ready(async() => {

  await mp();
  
  // $('#pagar').on('click', event => {
  //   api_base.get('/services/mercadopago/createPreference').then(async(response) => {
  //     console.log(response)
  //   })
  // })
  // const mp = new MercadoPago('TEST-bbffde91-98e7-43ad-b0cd-1d7ff6a17203');
  // const bricksBuilder = mp.bricks();

  // mp.bricks().create("wallet", "wallet_container", {
  //   initialization: {
  //     preferenceId: "469788790-608c3410-bc33-4627-841e-f7709d0fd4a7",
  //   },
  //   customization: {
  //     texts: {
  //       valueProp: 'smart_option',
  //     }
  //   }
  // })
  // await api_base.get('tokenCSRF');
  
  // const body = {"email": "lucas10_silva@hotmail.com", "senha": "123456789", "confirmarSenha": "123456789"}
  // await api_base.post('signin/register', body)

  // const body = {"email": "vini10_silva@hotmail.com", "senha": "123456789"}
  // const login = await api_base.post('signin', body);
  // console.log(login)

  // const users = await api_base.post('users');
  // console.log(users)  
  // const user = await api_base.postget('users/2');
  // console.log(user)  

})