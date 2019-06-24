import { connect } from 'src/drivers';
import SignIn from './signIn';
import { simiSignedIn } from 'src/simi/Redux/actions/simiactions';

const mapStateToProps = ({ user }) => {
    const { isSigningIn } = user;

    return {
        isSigningIn
    };
};

const mapDispatchToProps = { simiSignedIn };

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(SignIn);
