// import { sweet_alert } from "./sweet_alert.js";

// VERIFICA A URL DE ORIGEM
const current_location = location.href.split("/", 3)
export const api_base = axios.create({
  baseURL: "http://localhost:8000/caroldance/",
  timeout: 60000,
  // withCredentials: true
});

export const api_base_login = axios.create({
  baseURL: "http://localhost:8000/caroldance/",
  timeout: 60000,
  withCredentials: true
});

export const api_mercado_pago = axios.create({
  baseURL: "https://api.mercadopago.com/v1/payments/search?external_reference=",
  timeout: 60000,
  headers: {
    "Authorization": "Bearer TEST-1796517318559502-021922-78f9db078fd106de5ca0b10851ca07d9-469788790",
    "Content-Type": "application/json"
  }
});

// MUDAR AO FAZER DEPLOY
// export const api_base_redefinir_senha = axios.create({
	// baseURL: current_location?.[0].includes("https") ? "/password/" : "/tew/password/"
// });

export const url_base_redirect = current_location?.[0] + "//" + current_location?.[2] + current_location?.[0].includes("https") ? "/" : "/sges/";

export const token_recaptcha = "6LduHdsfAAAAACxpcu-xjT1Zf1TzVkPZqa5JygNZ";

export let pendingRequests = 0

api_base.interceptors.response.use((config) => {
  // if(config.config.url != 'autocomplete') pendingRequests--
  const {
    headers: { authorization },
    data: { code },
  } = config;

  // localStorage.setItem("token", authorization);

  // if (code == 498) {
  //   return sweet_alert("Erro!", "Sessão expirada!", "error", "Confirmar").then(() => {
  //     localStorage.clear();
  //     window.location.href = `${url_base_redirect}login`;
  //   });
  //   return config;
  // }
  // if (!!!authorization) {
  //   return config;
  // }
  // return config;
});

api_base.interceptors.request.use(async(config) => {
  switch(config.url){
    case 'signin':
    case 'signin/register':
      await api_base.get('token/csrf');
  }
  return config;
});

api_base_login.interceptors.response.use((config) => {
  const {
    headers: { authorization },
    data: { code },
    data,
  } = config;
  return config;
});

