import type { File } from "@/types/File";

export interface Folder {
  id: number;
  name: string;
  parent_id: number | null;
  created_by: number;
  children_count?: number;
  files_count?: number;
  children?: Folder[];
  files?: File;
  created_at: string;
  updated_at: string;
}

export interface Breadcrumb {
  id: number;
  name: string;
}
