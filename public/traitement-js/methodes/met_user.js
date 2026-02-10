
async function getUserById(id_user) {
    const user = await fetch(`/api/get/user/${id_user}`);
    if (!user.ok) {
        alert("Echec de getUserById "+id_user); 
        return null;
    }

    const data = await user.json();
    return data;
}