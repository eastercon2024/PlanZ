import store from './store';
import axios from 'axios';
import { setVolunteerJobs, setVolunteerShifts } from './volunteerActions';

export function fetchJobs() {
    axios.get('/api/volunteer/get_volunteer_jobs.php')
        .then(res => {
            store.dispatch(setVolunteerJobs(res.data));
        })
        .catch(error => {
            let message = "The list of jobs could not be downloaded."
            store.dispatch(setVolunteerJobs({}, message));
        }
    );
}

export function fetchShifts() {
    axios.get('/api/volunteer/get_volunteer_shifts.php')
        .then(res => {
            store.dispatch(setVolunteerShifts(res.data));
        })
        .catch(error => {
            let message = "The list of shifts could not be downloaded."
            store.dispatch(setVolunteerShifts({}, message));
        }
    );
}
