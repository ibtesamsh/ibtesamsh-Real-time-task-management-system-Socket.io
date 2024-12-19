import {Task} from "../models/Task.js";
// import {user} from "../models/user.js";

// Create Task
const createTask = async (req, res) => {
  const { title, description, priority, assignee, deadline } = req.body;
  console.log(req.body)

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
    res.status(201).json({ message: "Task created successfully", task: newTask });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};



// const createTask = async (req, res, io) => {
//   const { title, description, priority, assignee, deadline } = req.body;

//   try {
//     const newTask = new Task({
//       title,
//       description,
//       priority,
//       assignee,
//       deadline,
//       createdBy: req.user._id,
//     });

//     await newTask.save();

//     // Emit socket event for real-time task creation
//     io.emit("taskCreated", newTask);

//     res.status(201).json({ message: "Task created successfully", task: newTask });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// Get All Tasks
// const getAllTasks = async (req, res) => {
//   try {
//     const tasks = await Task.find()
//       .populate("assignee", "name email")
//       .populate("createdBy", "name email");
//     res.status(200).json(tasks);
//   } catch (error) {
//     res.status(500).json({ error: error.message });
//   }
// };
// Get All Tasks (with optional assignee filtering)
const getAllTasks = async (req, res) => {
  const { assignee } = req.query; // Get assignee from query params
  
  try {
    let tasks;

    if (assignee) {
      // Filter tasks by assignee
      tasks = await Task.find({ assignee })
        .populate("assignee", "name email")
        .populate("createdBy", "name email");
    } else {
      // If no assignee is passed, return all tasks
      tasks = await Task.find()
        .populate("assignee", "name email")
        .populate("createdBy", "name email");
    }

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
    const updatedTask = await Task.findByIdAndUpdate(
      id,
      { status },
      { new: true }
    );
    if (!updatedTask) {
      return res.status(404).json({ message: "Task not found" });
    }
    res.status(200).json({ message: "Task status updated", task: updatedTask });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};






// const updateTaskStatus = async (req, res, io) => {
//   const { id } = req.params;
//   const { status } = req.body;

//   try {
//     const updatedTask = await Task.findByIdAndUpdate(
//       id,
//       { status },
//       { new: true }
//     );

//     if (!updatedTask) {
//       return res.status(404).json({ message: "Task not found" });
//     }

//     Emit socket event for real-time task status updates
//     io.emit("taskUpdated", updatedTask);

//     res.status(200).json({ message: "Task status updated", task: updatedTask });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// Delete Task
const deleteTask = async (req, res) => {
  const { id } = req.params;

  try {
    const deletedTask = await Task.findByIdAndDelete(id);
    if (!deletedTask) {
      return res.status(404).json({ message: "Task not found" });
    }
    res.status(200).json({ message: "Task deleted successfully" });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};

// Assign Task to User
const assignTask = async (req, res) => {
  const { id } = req.params;
  const { assignee } = req.body;

  try {
    const task = await Task.findByIdAndUpdate(
      id,
      { assignee },
      { new: true }
    ).populate("assignee", "name email");
    if (!task) {
      return res.status(404).json({ message: "Task not found" });
    }
    res.status(200).json({ message: "Task assigned successfully", task });
  } catch (error) {
    res.status(400).json({ error: error.message });
  }
};
export{createTask,getAllTasks,updateTaskStatus,deleteTask,assignTask}



// import { Task } from "../models/Task.js";

// // Create Task
// const createTask = async (req, res) => {
//   const { title, description, priority, assignee, deadline } = req.body;
//   const io = req.io;  // Get the io object from the request

//   try {
//     const newTask = new Task({
//       title,
//       description,
//       priority,
//       assignee,
//       deadline,
//       createdBy: req.user._id, // Assuming `req.user` has the authenticated user info
//     });

//     await newTask.save();

//     // Emit real-time event for task creation
//     io.emit("taskCreated", newTask);

//     res.status(201).json({ message: "Task created successfully", task: newTask });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// // Get All Tasks (with optional assignee filtering)
// const getAllTasks = async (req, res) => {
//   const { assignee } = req.query;
//   const io = req.io;  // Get the io object from the request

//   try {
//     let tasks;

//     if (assignee) {
//       // Filter tasks by assignee
//       tasks = await Task.find({ assignee })
//         .populate("assignee", "name email")
//         .populate("createdBy", "name email");
//     } else {
//       // Retrieve all tasks
//       tasks = await Task.find()
//         .populate("assignee", "name email")
//         .populate("createdBy", "name email");
//     }

//     // Emit the tasks to all connected clients in real-time
//     io.emit("tasksUpdated", tasks);

//     res.status(200).json(tasks);
//   } catch (error) {
//     res.status(500).json({ error: error.message });
//   }
// };

// // Update Task Status
// const updateTaskStatus = async (req, res) => {
//   const { id } = req.params;
//   const { status } = req.body;
//   const io = req.io;  // Get the io object from the request

//   try {
//     const updatedTask = await Task.findByIdAndUpdate(
//       id,
//       { status },
//       { new: true }
//     );

//     if (!updatedTask) {
//       return res.status(404).json({ message: "Task not found" });
//     }

//     // Emit real-time event for task status update
//     io.emit("taskUpdated", updatedTask);

//     res.status(200).json({ message: "Task status updated", task: updatedTask });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// // Delete Task
// const deleteTask = async (req, res) => {
//   const { id } = req.params;
//   const io = req.io;  // Get the io object from the request

//   try {
//     const deletedTask = await Task.findByIdAndDelete(id);

//     if (!deletedTask) {
//       return res.status(404).json({ message: "Task not found" });
//     }

//     // Emit real-time event for task deletion
//     io.emit("taskDeleted", { id });

//     res.status(200).json({ message: "Task deleted successfully" });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// // Assign Task to User
// const assignTask = async (req, res) => {
//   const { id } = req.params;
//   const { assignee } = req.body;
//   const io = req.io;  // Get the io object from the request

//   try {
//     const task = await Task.findByIdAndUpdate(
//       id,
//       { assignee },
//       { new: true }
//     ).populate("assignee", "name email");

//     if (!task) {
//       return res.status(404).json({ message: "Task not found" });
//     }

//     // Emit real-time event for task assignment
//     io.emit("taskAssigned", task);

//     res.status(200).json({ message: "Task assigned successfully", task });
//   } catch (error) {
//     res.status(400).json({ error: error.message });
//   }
// };

// export { createTask, getAllTasks, updateTaskStatus, deleteTask, assignTask };

