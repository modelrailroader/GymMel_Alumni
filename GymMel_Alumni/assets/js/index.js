import { handleCreateUser, handleEditUser, handleShowData, handleUcp, handleShowUsers, handleFindDuplicates } from './content';
import { handlePasswordToggles } from "./utils/password";

// User administration
handleShowUsers();
handleCreateUser();
handleEditUser();
handleUcp();

// Network administration
handleShowData();
handleFindDuplicates();

// Handle Show-password buttons
handlePasswordToggles();