import type { File } from "@/types/File";
import type { Folder } from "@/types/Folder";

export * from "./Department";
export * from "./User";
export * from "./File";
export * from "./Folder";

export interface Paginated<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export type ExplorerItem = { kind: "folder"; data: Folder } | { kind: "file"; data: File };

export interface ApiErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}
