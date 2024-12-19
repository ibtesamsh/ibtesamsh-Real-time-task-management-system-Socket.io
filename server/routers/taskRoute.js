import { Router } from "express";
import auth from "../Auth.js";
import {
  createTask,
  getAllTasks,
  updateTaskStatus,
  deleteTask,
  assignTask,
} from "../controller/taskController.js";

const router = Router();

router.route("/").post(auth, createTask); // Create a new task
router.route("/").get(auth, getAllTasks); // Get all tasks
router.route("/:id/status").put(auth, updateTaskStatus); // Update task status
router.route("/:id").delete(auth, deleteTask); // Delete a specific task
router.route("/:id/assign").put(auth, assignTask); // Assign task to a user

export default router;
// import { Router } from "express";
// import auth from "../Auth.js";
// import {
//   createTask,
//   getAllTasks,
//   updateTaskStatus,
//   deleteTask,
//   assignTask,
// } from "../controller/taskController.js";

// const router = Router();

// // Define the task routes
// const taskRoutes = () => {
//   // Create a new task
//   router.route("/").post(auth, createTask);

//   // Get all tasks (optionally filtered by assignee)
//   router.route("/").get(auth, getAllTasks);

//   // Update task status
//   router.route("/:id/status").put(auth, updateTaskStatus);

//   // Delete a specific task
//   router.route("/:id").delete(auth, deleteTask);

//   // Assign task to a user
//   router.route("/:id/assign").put(auth, assignTask);

//   return router;
// };

// export default taskRoutes;
