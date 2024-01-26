import { handleCreateUser, handleEditUser, handleShowData, handleUcp, handleShowUsers } from './content';
import { handlePasswordToggles } from "./utils/password";

// User administration
handleShowUsers();
handleCreateUser();
handleEditUser();
handleUcp();

// Network administration
handleShowData();

// Handle Show-password buttons
handlePasswordToggles();