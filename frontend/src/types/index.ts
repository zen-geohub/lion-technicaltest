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

export interface ApiErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}
