import type { Department } from "@/types/Department";
import type { Folder } from "@/types/Folder";
import type { User } from "@/types/User";

export interface FileItem {
  id: number;
  folder_id: number;
  title: string;
  department_id: number;
  original_name: string;
  file_path: string;
  mime_type: string | null;
  size: number;
  uploaded_by: number;
  department?: Department;
  folder?: Folder;
  uploader?: User;
  created_at: string;
  updated_at: string;
}
