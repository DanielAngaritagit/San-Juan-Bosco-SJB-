import express from 'express';
import bodyParser from 'body-parser';
import passwordRoutes from './routes/passwordRoutes';
import { createConnection } from './db/index';

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Database connection
createConnection();

// Routes
app.use('/api/password', passwordRoutes);

// Start the server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});