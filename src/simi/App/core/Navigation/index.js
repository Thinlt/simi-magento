export { default as Navigation } from './navigation';

import { connect } from 'src/drivers';
import { closeDrawer } from 'src/actions/app';
import { getUserDetails } from 'src/actions/user';
import Navigation from './navigation';

const mapStateToProps = ({ app, user }) => {
    const { currentUser, isSignedIn } = user;
    const { drawer } = app
    return {
        drawer,
        currentUser,
        isSignedIn
    }
}
const mapDispatchToProps = {
    closeDrawer,
    getUserDetails
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Navigation);
