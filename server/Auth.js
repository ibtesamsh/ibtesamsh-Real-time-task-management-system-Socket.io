import jwt from "jsonwebtoken";


const auth=async(req,res,next)=>{
    // const token = req.cookies?.token;
    const token = req.cookies?.token||req.headers?.authorization?.split(" ")[1]; 
    try{
        if(!token){
            return res.status(401).json({msg:"Please login first"})
        }
        jwt.verify(token,process.env.JWT_SECRET,(err,decoded)=>{
            if(err){
                return res.status(401).json({msg:"Token is invalid"})
                }
                // req.user_id=decoded?.id
                req.user=decoded;
                next()

        })
        
    }
    catch (err){
        console.log("error",err)
        return res.status(500).json({msg:"Server error"})
    }
}
export default auth;