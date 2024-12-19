import { user } from "../models/user.model.js";
import jwt from "jsonwebtoken";
import auth from '../Auth.js';
import bcrypt from "bcrypt";
import {redis} from "../index.js"

// const login = async (req, res) => {
//   try {
//     const { email, password } = req.body;
//     const result = await user.findOne({ email });
//     if (!result) {
//        return res.status(400).json({ message: "invalid email or password" });
//     }

//     const isValidPassword = await result.comparePassword(password);
//     if (!isValidPassword) {
//      return res.status(400).json({ message: " password is incorrect" });
//     }
//     const token = await result.generatetoken();
   
//     res.cookie("token", token);

//     return res.status(200).json({ message: "user loged in", token});
//     // const token=await user.generateToken
//   } catch (err) {
//     console.log(err);
//     res.status(500).send({ message: "internal sever error" });
//   }
// };

// const registerUser = async (req, res) => {
//   try {
//     const { name, email, password } = req.body;
//     if ([email, name, password].some((field) => field?.trim() === ""))
//       return res.status(400).json({ message: "fiels is empty" });

//     const existingUser = await user.findOne({ email });

//     if (existingUser) {
//       return res.status(400).json({ message: "user already exist" });
//     }

//     const userCreate = await user.create({name, email, password });
//     await userCreate.save();
    
//     return res.status(200).json({ message: "user created successfully" });
//   } catch (err) {
//     console.log(err);
//     res.status(500).send({ message: "internal sever error" });
//   }
  
// };
// const logout = async (req, res) => {
//   try {
//     res.clearCookie("token");
//     return res.status(200).json({ message: "user logged out" });
//   } catch (err) {
//     console.log(err);
//     res.status(500).send({ message: "internal sever error" });
//   }
// };

// // const dashboard = async(req,res)=>{
// //   try{
    
// //     const Users = await user.find({})
// //     return res.status(200).json(Users)
// // }
// // catch(err){
// //   console.log(err)
// //   res.status(500).send({message:"internal sever error"})
// // }
// // };
// const dashboard = async (req, res) => {
//   try {
    
//     const cachedUsers = await redis.get('users_data');
    
//     if (cachedUsers) {
      
//       console.log('Fetching from cache');
//       return res.status(200).json(JSON.parse(cachedUsers));
//     }

    
//     const users = await user.find({});
    
    
//     await redis.setex('users_data', 60, JSON.stringify(users));
    
//     console.log('Fetching from database');
    
//     return res.status(200).json(users);
//   } catch (err) {
//     console.error(err);
//     return res.status(500).send({ message: "Internal Server Error" });
//   }
// };



// // Edit user details
// const editUser = async (req, res) => {
//   try {
//   // Get the user ID from the request parameters
//     const { id, name, email } = req.body;

//     // Check if any field is provided in the request body
//     if ([name, email].every((field) => field?.trim() === "")) {
//       return res.status(400).json({ message: "No fields to update" });
//     }

//     // Find the user by their ID
//     const existingUser = await user.findById(id);
//     if (!existingUser) {
//       return res.status(404).json({ message: "User not found" });
//     }

//     // Update the user fields if provided
//     if (name) existingUser.name = name;
//     if (email) existingUser.email = email;
//     // You may want to hash this password before saving

//     // Save the updated user
//     await existingUser.save();

//     return res.status(200).json({ message: "User updated successfully", user: existingUser });
//   } catch (err) {
//     console.log(err);
//     res.status(500).send({ message: "Internal server error" });
//   }
// };
// //----------------------------delete------------------


// const deleteUser = async (req, res) => {
//   try {
//     // The middleware will ensure that the token is valid, and user_id will be added to the request object
//     const { id } = req.params;
    
//     // Find the user by their ID in the database
//     const existingUser = await user.findByIdAndDelete({_id:id});
//     if (!existingUser) {
//       return res.status(404).json({ message: "User not found" });
//     }

//     // Delete the user from the database
//     await user.deleteOne({ _id: id });

//     return res.status(200).json({ message: "User deleted successfully" });
//   } catch (err) {
//     console.log(err);
//     res.status(500).send({ message: "Internal server error" });
//   }
// };


// //----------------------------bulk------------------
// const bulkRegister = async (req, res) => {
 
//     const users = req.body; 
//     // console.log(users);
//     try {
//       const hashedUsers = await Promise.all(users.map(async (element) => {
//         const hashPassword = await bcrypt.hash(element.password, 10);
//         return { ...element, password: hashPassword }; 
//     }));
    
//       const result = await user.insertMany(hashedUsers);
//       res.status(200).json({ success: true, insertedCount: result.insertedCount });
//       } catch (err) {
//         res.status(500).json({ success: false, error: err.message
//           });
//           }
// }



// export { login, registerUser,logout ,dashboard,editUser,deleteUser,bulkRegister};
const login = async (req, res) => {
  try {
    const { email, password } = req.body;

    console.log(`Login attempt for email: ${email}`); // Debug log

    // Step 1: Find user
    const userFound = await user.findOne({ email });
    if (!userFound) {
      console.log("User not found"); // Debug log
      return res.status(400).json({ message: 'Invalid email or password' });
    }

    console.log("Stored hash in DB:", userFound.password); // Log stored hash
    console.log("Entered password:", password); // Log entered password

    // Step 2: Compare password
    const isPasswordValid = await userFound.comparePassword(password);
    console.log("Password comparison result:", isPasswordValid); // Log result of comparison

    if (!isPasswordValid) {
      console.log("Password mismatch"); // Debug log
      return res.status(400).json({ message: 'Password is incorrect' });
    }

    console.log("Password matched, generating token..."); // Debug log

    // Step 3: Generate JWT Token
    const token = jwt.sign(
      { _id: userFound._id, name:userFound.name, role: userFound.role, email: userFound.email },
      process.env.JWT_SECRET
    );

    // Step 4: Set token as cookie
    res.cookie('token', token, { httpOnly: true });
    console.log("Login successful"); // Debug log

    return res.status(200).json({ message: 'User logged in successfully', token });
  } catch (err) {
    console.error("Error during login:", err); // Debug log
    res.status(500).send({ message: 'Internal server error' });
  }
};



const registerUser = async (req, res) => {
  try {
    const { name, email, password, role } = req.body;
    if (!name || !email || !password || !role) {
      return res.status(400).json({ message: 'All fields are required' });
    }

    const existingUser = await user.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: 'User already exists' });
    }

    // const salt = await bcrypt.genSalt(10);
    // const hashedPassword = await bcrypt.hash(password, salt);

    const newUser = new user({ name, email, password, role });
    await newUser.save();

    res.status(201).json({ message: 'User registered successfully' });
  } catch (err) {
    console.error(err);
    res.status(500).send({ message: 'Internal server error' });
  }
};

const logout = async (req, res) => {
  try {
    res.clearCookie('token');
    return res.status(200).json({ message: 'User logged out successfully' });
  } catch (err) {
    console.error(err);
    res.status(500).send({ message: 'Internal server error' });
  }
};

export{login,registerUser,logout}