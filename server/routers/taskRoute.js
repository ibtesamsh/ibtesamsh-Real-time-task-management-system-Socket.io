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

// Attach routes with auth middleware
router.route("/")
  .post(auth, createTask) // Create Task
  .get(auth, getAllTasks); // Get Tasks

router.route("/:id/status")
  .put(auth, updateTaskStatus); // Update Task Status

router.route("/:id")
  .delete(auth, deleteTask); // Delete Task

router.route("/:id/assign")
  .put(auth, assignTask); // Assign Task

export default router;

