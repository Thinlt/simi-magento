class Customer {

    static isLogin() {
        if (sessionStorage.getItem('email')) {
            return true;
        }
        return false;
    }
}

export default Customer;
