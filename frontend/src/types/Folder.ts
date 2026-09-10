import type { File } from "@/types/File";

export interface Folder {
  id: number;
  name: string;
  slug: string;
  parent_id: number | null;
  created_by: number;
  children_count?: number;
  files_count?: number;
  children?: Folder[];
  files?: File;
  created_at: string;
  updated_at: string;
}

export interface BreadcrumbEntry {
  id: number;
  name: string;
  slug: string;
  path: string;
}

export interface FolderResponse {
  folder: Folder;
  breadcrumb: BreadcrumbEntry[];
}
