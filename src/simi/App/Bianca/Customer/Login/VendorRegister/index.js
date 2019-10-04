import { connect } from 'src/drivers';
import VendorRegister from './VendorRegister';

const mapStateToProps = ({ user }) => {
    const { createAccountError } = user;
    return {
        createAccountError
    };
};

export default connect(mapStateToProps)(VendorRegister);