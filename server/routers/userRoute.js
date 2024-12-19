import { Router } from "express";
import auth from "../Auth.js";

import { login, registerUser,logout} from "../controller/userControllere.js";
const router = Router()



router.post('/login', login);
router.post('/register', registerUser);
router.post('/logout', auth,logout);
export default router;