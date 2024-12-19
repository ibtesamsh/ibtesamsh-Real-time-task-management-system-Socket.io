import mongoose from "mongoose";

const TaskSchema = new mongoose.Schema({
    title: { type: String, required: true },
    description: { type: String },
    status: { type: String, enum: ['pending', 'in_progress', 'completed'], default: 'pending' },
    priority: { type: String, enum: ['low', 'medium', 'high'], default: 'medium' },
    assignee: { type: mongoose.Schema.Types.ObjectId, ref: 'user' },
    createdBy: { type: mongoose.Schema.Types.ObjectId, ref: 'user' },
    deadline: { type: Date },
  }, { timestamps: true });
  export const Task =  mongoose.model("Task", TaskSchema)