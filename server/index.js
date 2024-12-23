import express from "express";
import cookieParser from "cookie-parser";
import dotenv from "dotenv";
import cors from "cors";
import connectDB from "./db.js";
import userRouter from "./routers/userRoute.js";
import taskRouter from "./routers/taskRoute.js";
import Redis from "ioredis";
import { Server as SocketIoServer } from "socket.io";
import http from "http";


dotenv.config({ path: "./.env" });

const app = express();
const server = http.createServer(app);

// Create Socket.IO server
const io = new SocketIoServer(server, {
  cors: { origin: process.env.CORS_ORIGIN || "*", methods: ["GET", "POST"] },
});

// Middleware setup
app.use(cors({ origin: process.env.CORS_ORIGIN || "*" }));
app.use(express.json({ limit: "750mb" }));
app.use(cookieParser());

// Redis connection
export const redis = new Redis({ host: "localhost", port: 6379 });
redis.on("connect", () => console.log("🚀 Redis connected"));

// Attach io instance to requests
app.use((req, res, next) => {
  req.io = io;
  next();
});

// Routes
app.use("/api/users", userRouter);
app.use("/api/tasks", taskRouter);

// Socket.IO events
io.on("connection", (socket) => {
  console.log("A user connected");

  socket.on("disconnect", () => {
    console.log("A user disconnected");
  });
  socket.on("sendMessage", (data) => {
    console.log(`Message received: ${data.message}`);
    io.emit("receiveMessage", {
      sender: data.sender,
      text: data.message,
    });
  });
});



// Start server
connectDB().then(() => {
  const PORT = process.env.PORT || 8000;
  server.listen(PORT, () => {
    console.log(`⚙️ Server running on port ${PORT}`);
  });
});





