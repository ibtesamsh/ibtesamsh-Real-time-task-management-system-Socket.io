// import jwt from "jsonwebtoken";


// const auth=async(req,res,next)=>{
//     // const token = req.cookies?.token;
//     const token = req.cookies?.token||req.headers?.authorization?.split(" ")[1]; 
//     try{
//         if(!token){
//             return res.status(401).json({msg:"Please login first"})
//         }
//         jwt.verify(token,process.env.JWT_SECRET,(err,decoded)=>{
//             if(err){
//                 return res.status(401).json({msg:"Token is invalid"})
//                 }
//                 // req.user_id=decoded?.id
//                 req.user=decoded;
//                 next()

//         })
        
//     }
//     catch (err){
//         console.log("error",err)
//         return res.status(500).json({msg:"Server error"})
//     }
// }
// export default auth;


import jwt from "jsonwebtoken";
import {user} from "./models/user.model.js"; // Correct import for User model

const auth = async (req, res, next) => {
  const token = req.cookies?.token || req.headers?.authorization?.split(" ")[1];

  try {
    if (!token) {
      return res.status(401).json({ msg: "Please login first" });
    }

    jwt.verify(token, process.env.JWT_SECRET, async (err, decoded) => {
      if (err) {
        return res.status(401).json({ msg: "Token is invalid" });
      }

      const authenticatedUser = await user.findById(decoded._id); // Find user by ID in token
      if (!authenticatedUser) {
        return res.status(401).json({ msg: "User not found" });
      }

      req.user = authenticatedUser; // Attach the full user object
      next();
    });
  } catch (err) {
    console.error("Authentication error:", err);
    res.status(500).json({ msg: "Server error" });
  }
};

export default auth;

