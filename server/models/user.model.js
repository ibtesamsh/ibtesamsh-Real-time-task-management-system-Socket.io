import mongoose from "mongoose";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";

const userSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: true,
      lowercase:true,
      trim:true
    },
    email: {
      type: String,
      required: true,
      unique: true,
      trim:true
    },
    password: {
      type: String,
      required: true,
      trim:true
    },
    role: {
      type: String,
      enum: ['admin', 'team_member'],
      default: "user"
      },

  },
  { timestamps: true }
);
userSchema.pre('save',async function(next){
  if(!this.isModified('password') ) return next();
    this.password = await bcrypt.hash(this.password,10)
    next()
})
userSchema.methods.comparePassword=async function(password){
    return bcrypt.compare(password,this.password);
}
// userSchema.methods.generatetoken= async function(){
//   try{ return jwt.sign({id:this._id,
//     name:this.name,
//     email:this.email
//   },process.env.JWT_SECRET)
// }

//   catch(err){
//     console.log("error",err)
//   }
// }
export const user =  mongoose.model("user", userSchema)