import { Task } from "../models/Task.js";

// Create Task
const createTask = async (req, res) => {
  const { title, description, priority, assignee, deadline } = req.body;

  try {
    const newTask = new Task({
      title,
      description,
      priority,
      assignee,
      deadline,
      createdBy: req.user._id,
    });

    await newTask.save();

    // Emit event for new task creation
    req.io.emit("taskCreated", newTask);

    res.status(201).json({ message: "Task created successfully", task: newTask });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};

// Get All Tasks
const getAllTasks = async (req, res) => {
  const { assignee } = req.query;

  try {
    const tasks = await Task.find(assignee ? { assignee } : {})
      .populate("assignee", "name email")
      .populate("createdBy", "name email");

    res.status(200).json(tasks);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
};

// Update Task Status
const updateTaskStatus = async (req, res) => {
  const { id } = req.params;
  const { status } = req.body;

  try {
    const updatedTask = await Task.findByIdAndUpdate(id, { status }, { new: true });

    if (!updatedTask) {
      return res.status(404).json({ message: "Task not found" });
    }

    // Emit event for status update
    req.io.emit("taskStatusUpdated", updatedTask);

    res.status(200).json({ message: "Task status updated", task: updatedTask });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};

// Delete Task
const deleteTask = async (req, res) => {
  const { id } = req.params;

  try {
    const deletedTask = await Task.findByIdAndDelete(id);

    if (!deletedTask) {
      return res.status(404).json({ message: "Task not found" });
    }

    // Emit event for task deletion
    req.io.emit("taskDeleted", id);

    res.status(200).json({ message: "Task deleted successfully" });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};

// Assign Task
const assignTask = async (req, res) => {
  const { id } = req.params;
  const { assignee } = req.body;

  try {
    const updatedTask = await Task.findByIdAndUpdate(id, { assignee }, { new: true })
      .populate("assignee", "name email");

    if (!updatedTask) {
      return res.status(404).json({ message: "Task not found" });
    }

    // Emit event for task assignment
    req.io.emit("taskAssigned", updatedTask);

    res.status(200).json({ message: "Task assigned successfully", task: updatedTask });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};

export { createTask, getAllTasks, updateTaskStatus, deleteTask, assignTask };


