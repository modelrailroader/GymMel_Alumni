import {
    handleCreateUser,
    handleEditUser,
    handleShowData,
    handleUcp,
    handleShowUsers,
    handleFindDuplicates,
    handleEditAlumni,
    handleChangeData,
    handleEmailToken,
    handleAddData
} from './content';
import { handlePasswordToggles } from "./utils/password";
import {easterEggs} from "./utils/easterEggs";

// Front form to add new alumni
handleAddData();

// User administration
handleShowUsers();
handleCreateUser();
handleEditUser();
handleUcp();

// Network administration
handleShowData();
handleFindDuplicates();
handleEditAlumni();

// Handle change data
handleChangeData();
handleEmailToken();

// Handle Show-password buttons
handlePasswordToggles();

// Handle Easter Eggs
easterEggs();