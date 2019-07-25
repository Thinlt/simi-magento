import React from 'react'
import { connect } from 'src/drivers';
import { signOut } from 'src/actions/user';
import Loading from 'src/simi/BaseComponents/Loading'

var loggingOut = false

const Logout = props => {
    const { signOut, history, isSignedIn } = props
    if (isSignedIn) {
        if (!loggingOut) {
            loggingOut = true;
            sessionStorage.removeItem("shipping_address");
            sessionStorage.removeItem("billing_address");
            signOut({ history })
        } else {
            console.log('Already logging out')
        }
    } else
        history.push('/')
    return <Loading />
}

const mapStateToProps = ({ user }) => {
    const { isSignedIn } = user
    return {
        isSignedIn
    };
}


export default connect(mapStateToProps, { signOut })(Logout);
