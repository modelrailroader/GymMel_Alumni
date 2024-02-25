import {
    handleCreateUser,
    handleEditUser,
    handleShowData,
    handleUcp,
    handleShowUsers,
    handleFindDuplicates,
    handleEditAlumni
} from './content';
import { handlePasswordToggles } from "./utils/password";
import {easterEggs} from "./utils/easterEggs";

// User administration
handleShowUsers();
handleCreateUser();
handleEditUser();
handleUcp();

// Network administration
handleShowData();
handleFindDuplicates();
handleEditAlumni();

// Handle Show-password buttons
handlePasswordToggles();

// Handle Easter Eggs
easterEggs();