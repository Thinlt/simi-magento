import { connect } from 'src/drivers';

import Header from './header';
import { toggleSearch } from 'src/actions/app';

const mapStateToProps = ({ app }) => {
    const { searchOpen, drawer } = app;
    return {
        drawer,
        searchOpen
    };
};

const mapDispatchToProps = { toggleSearch };

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Header);
