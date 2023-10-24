import axios from "axios";
const md5 = require('md5');

const baseDomain = ".";
const baseUrl = baseDomain+"/api/";
const mask_token = md5(window.CLEAN_TOKEN);

export default axios.create({baseUrl, headers:{Authorization:mask_token}})
//export default axios.create({baseURL, headers:{}})