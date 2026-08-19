const request = require('supertest');
const express = require('express');
const { expect } = require('chai');
const { MongoMemoryServer } = require('mongodb-memory-server');
const mongoose = require('mongoose');
const User = require('../backend/models/User');
const Jewellery = require('../backend/models/Jewellery');

const userRoutes = require('../backend/routes/userRoutes');
const jewelleryRoutes = require('../backend/routes/jewelleryRoutes');

const app = express();
app.use(express.json());
app.use('/api/users', userRoutes);
app.use('/api/jewellery', jewelleryRoutes);

let mongoServer;

before(async () => {
    mongoServer = await MongoMemoryServer.create();
    const uri = mongoServer.getUri();
    await mongoose.connect(uri);
});

after(async () => {
    await mongoose.disconnect();
    await mongoServer.stop();
});

afterEach(async () => {
    const collections = mongoose.connection.collections;
    for (const key in collections) {
        await collections[key].deleteMany();
    }
});

describe('Backend API Tests', () => {
    let manufacturerToken;
    let customerToken;
    let manufacturerId;

    beforeEach(async () => {
        // Register Manufacturer
        const mfgRes = await request(app).post('/api/users/register').send({
            name: 'Test Manufacturer',
            email: 'mfg@test.com',
            password: 'password123',
            role: 'Manufacturer'
        });
        manufacturerToken = mfgRes.body.token;
        manufacturerId = mfgRes.body._id;

        // Register Customer
        const custRes = await request(app).post('/api/users/register').send({
            name: 'Test Customer',
            email: 'cust@test.com',
            password: 'password123',
            role: 'Customer'
        });
        customerToken = custRes.body.token;
    });

    describe('User Routes', () => {
        it('should login an existing user', async () => {
            const res = await request(app).post('/api/users/login').send({
                email: 'mfg@test.com',
                password: 'password123'
            });
            expect(res.status).to.equal(200);
            expect(res.body).to.have.property('token');
        });

        it('should fetch current user profile', async () => {
            const res = await request(app)
                .get('/api/users/me')
                .set('Authorization', `Bearer ${manufacturerToken}`);
            expect(res.status).to.equal(200);
            expect(res.body.email).to.equal('mfg@test.com');
        });
    });

    describe('Jewellery Routes', () => {
        it('should allow manufacturer to add jewellery', async () => {
            const res = await request(app)
                .post('/api/jewellery')
                .set('Authorization', `Bearer ${manufacturerToken}`)
                .send({
                    tokenId: '1',
                    name: 'Gold Ring',
                    category: 'Ring',
                    weight: 10.5,
                    purity: '22K',
                    hallmark: 'HLM123',
                    ipfsHash: 'ipfs://testHash'
                });
            expect(res.status).to.equal(201);
            expect(res.body.name).to.equal('Gold Ring');
        });

        it('should not allow customer to add jewellery', async () => {
            const res = await request(app)
                .post('/api/jewellery')
                .set('Authorization', `Bearer ${customerToken}`)
                .send({
                    tokenId: '2',
                    name: 'Fake Ring',
                    category: 'Ring',
                    weight: 10.5,
                    purity: '22K',
                    hallmark: 'HLM456',
                    ipfsHash: 'ipfs://testHash'
                });
            expect(res.status).to.equal(403);
        });

        it('should fetch jewellery by token ID', async () => {
            await request(app)
                .post('/api/jewellery')
                .set('Authorization', `Bearer ${manufacturerToken}`)
                .send({
                    tokenId: '10',
                    name: 'Diamond Necklace',
                    category: 'Necklace',
                    weight: 50,
                    purity: '18K',
                    hallmark: 'DIA123',
                    ipfsHash: 'ipfs://hash'
                });

            const res = await request(app).get('/api/jewellery/10');
            expect(res.status).to.equal(200);
            expect(res.body.name).to.equal('Diamond Necklace');
            expect(res.body.manufacturer.email).to.equal('mfg@test.com');
        });
    });
});
