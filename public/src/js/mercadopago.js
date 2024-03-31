export const mp = new MercadoPago('TEST-bbffde91-98e7-43ad-b0cd-1d7ff6a17203');
// const bricksBuilder = mp.bricks();

import { api_mercado_pago } from "./api.js";

mp.bricks().create("wallet", "wallet_container", {
  initialization: {
    amount: 100,
    preferenceId: "469788790-2adc7db2-bce1-42a4-8d4c-92e76246423f",
    redirectMode: "blank"
  },
  customization: {
    texts: {
      valueProp: 'smart_option',
    }
  },
  callbacks: {
    onReady: () => {
      /*
       Callback chamado quando o Brick estiver pronto.
       Aqui você pode ocultar loadings do seu site, por exemplo.
      */
    },
    onSubmit: () => {
      $("#wallet_container").attr('disabled', 'disabled')
      Swal.fire({
        title: 'Aguarde...',
        html: 'Estamos validando seu pedido. Isso pode levar alguns minutos!',
        icon: 'info',
        backdrop: 'backdrop-filter: blur(4px)',
        showConfirmButton: true,
        // showCancelButton: !!cancelText,
        confirmButtonColor: '#6693ED',
        confirmButtonText: 'OK',
        // cancelButtonText: 'C',
        allowEscapeKey: false,
        allowOutsideClick: false,
        allowEnterKey: false,
      });
      api_mercado_pago.get('sdsdw11').then(response => {
        console.log(results)
      })
    },
    onError: (error) => {
      // callback chamado para todos os casos de erro do Brick
      console.error(error);
    },
  },
})